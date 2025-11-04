import ComponentCard from "@/components/common/ComponentCard.jsx";
import Input from "@/components/common/InputField.jsx";
import Label from "@/components/common/Label.jsx";
import GroupsAPI from "@/services/api/GroupsAPI.js";
import { useState, useEffect, useCallback } from "react";
import { useNavigate, useParams } from "react-router-dom";
import TextArea from "@/components/common/TextArea.jsx";
import ImageUpload from "@/components/common/ImageUpload.jsx";
import Select from "@/components/common/Select.jsx";
import Radio from "@/components/common/Radio.jsx";
import QuestionAPI from "@/services/api/QuestionAPI.js";

export default function RoadSignAddEdit() {
    const { id } = useParams();
    const isEditMode = Boolean(id);
    const navigate = useNavigate();

    // State management
    const [question, setQuestion] = useState("");
    const [categoryId, setCategoryId] = useState("");
    const [answers, setAnswers] = useState([{ option_text: "", is_correct: false }, { option_text: "", is_correct: false }]);
    const [errorMessages, setErrorMessages] = useState({});
    const [successMessage, setSuccessMessage] = useState("");
    const [categories, setCategories] = useState([]);
    const [questionImage, setQuestionImage] = useState(null);
    const [isLoading, setIsLoading] = useState(false);

    // Fetch categories
    const getCategories = useCallback(async () => {
        try {
            const resp = await GroupsAPI.getCategoryList();
            console.log(resp);
            setCategories(resp.groups);
        } catch (error) {
            console.error("Failed to fetch categories:", error);
        }
    }, []);

    // Fetch question data in edit mode
    const fetchQuestion = useCallback(async () => {
        if (!isEditMode) return;
        setIsLoading(true);
        try {
            const res = await QuestionAPI.getQuestion(id);
            const {  text, category_id, answers: apiAnswers, image } = res.data;
             setQuestion(text || "");
            setCategoryId(category_id || "");
            setAnswers(
                apiAnswers.map((answer) => ({
                    option_text: answer.translation.title,
                    is_correct: answer.is_right === 1,
                }))
            );
            setQuestionImage(image ? { url: image } : null);
        } catch (err) {
            console.error("Failed to fetch question:", err);
            navigate("/questions", { state: { errorMessage: "Հարցը չի գտնվել։" } });
        } finally {
            setIsLoading(false);
        }
    }, [id, isEditMode, navigate]);

    useEffect(() => {
        getCategories();
        fetchQuestion();
    }, [getCategories, fetchQuestion]);

    // Form validation
    const validateForm = () => {
        const newErrorMessages = {};
        let valid = true;

        if (!question.trim()) {
            valid = false;
            newErrorMessages.question = "Հարցը պարտադիր է։";
        }

        if (!categoryId) {
            valid = false;
            newErrorMessages.category = "Խումբը պարտադիր է։";
        }

        answers.forEach((answer, index) => {
            if (!answer.option_text.trim()) {
                valid = false;
                newErrorMessages[`answer${index + 1}`] = `Պատասխան ${index + 1} պարտադիր է։`;
            }
        });

        if (!answers.some((answer) => answer.is_correct)) {
            valid = false;
            newErrorMessages.correctAnswer = "Խնդրում ենք ընտրել ճիշտ պատասխան։";
        }

        setErrorMessages(newErrorMessages);
        return valid;
    };

    // Form submission
    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrorMessages({});
        setSuccessMessage("");

        if (!validateForm()) return;

        setIsLoading(true);
        try {

            const formData = new FormData();
            formData.append("question", question);
            formData.append("group_id", categoryId);
            answers.forEach((answer, index) => {
                formData.append(`answers[${index}][option_text]`, answer.option_text);
                formData.append(`answers[${index}][is_correct]`, answer.is_correct ? 1 : 0);

            });
            if (questionImage && questionImage instanceof File) {
                formData.append("question_image", questionImage);
            }


            const response = isEditMode
                ? await QuestionAPI.updateQuestion(id, formData)
                : await QuestionAPI.createQuestion(formData);

            if ([200, 201].includes(response?.status)) {
                navigate("/questions", {
                    state: {
                        successMessage: isEditMode
                            ? "Հարցը հաջողությամբ խմբագրվեց։"
                            : "Հարցը հաջողությամբ ավելացվեց։",
                    },
                });

                if (!isEditMode) {
                    setQuestion("");
                    setCategoryId("");
                    setAnswers([{ option_text: "", is_correct: false }, { option_text: "", is_correct: false }]);
                    setQuestionImage(null);
                }
            }
        } catch (err) {
            console.error("Submission error:", err);
            setErrorMessages({ general: err.message || "Ինչ-որ բան սխալ է։ Խնդրում ենք կրկին փորձել։" });
        } finally {
            setIsLoading(false);
        }
    };

    // Handlers
    const handleFileChange = (file) => setQuestionImage(file);
    const handleAnswerChange = (index, value) => {
        const newAnswers = [...answers];
        newAnswers[index] = { ...newAnswers[index], option_text: value };
        setAnswers(newAnswers);
    };

    const handleCorrectAnswerChange = (index) => {
        const newAnswers = answers.map((answer, i) => ({
            ...answer,
            is_correct: i === index,
        }));
        setAnswers(newAnswers);
    };

    const addAnswer = () => {
        setAnswers([...answers, { option_text: "", is_correct: false }]);
    };

    const removeAnswer = (index) => {
        if (answers.length <= 2) return; // Ensure at least 2 answers remain
        const newAnswers = answers.filter((_, i) => i !== index);
        setAnswers(newAnswers);
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <ComponentCard title={isEditMode ? "Խմբագրել հարց" : "Ավելացնել հարց"}>
                {isLoading && <p>Բեռնվում է...</p>}
                <div>
                    <Label htmlFor="question">Հարց</Label>
                    <TextArea
                        id="question"
                        value={question}
                        onChange={setQuestion}
                        placeholder="Հարց"
                        rows={6}
                        error={errorMessages.question}
                        hint={errorMessages.question || ""}
                        disabled={isLoading}
                    />
                </div>

                <div>
                    <Label htmlFor="category">Խումբ</Label>
                     <Select
                        id="category"
                        options={categories}
                        placeholder="Ընտրել խումբ"
                        onChange={(value) => setCategoryId(value)}
                        defaultValue={categoryId}
                        error={errorMessages.category}
                        hint={errorMessages.category || ""}
                        disabled={isLoading}
                    />
                </div>

                <div>
                    <Label>Հարցի նկար</Label>
                    <ImageUpload
                        onChange={handleFileChange}
                        value={questionImage?.url}
                        disabled={isLoading}
                    />
                </div>

                {successMessage && (
                    <p className="text-sm text-green-600 dark:text-green-400">{successMessage}</p>
                )}
                {errorMessages.general && (
                    <p className="text-sm text-red-600 dark:text-red-400">{errorMessages.general}</p>
                )}
            </ComponentCard>

            <ComponentCard title={isEditMode ? "Խմբագրել պատասխաններ" : "Ավելացնել պատասխաններ"}>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {answers.map((answer, index) => (
                        <div key={index}>
                            <Label htmlFor={`answer${index + 1}`}>Պատասխան {index + 1}</Label>
                            <Input
                                type="text"
                                id={`answer${index + 1}`}
                                value={answer.option_text}
                                onChange={(e) => handleAnswerChange(index, e.target.value)}
                                placeholder={`Պատասխան ${index + 1}`}
                                error={errorMessages[`answer${index + 1}`]}
                                hint={errorMessages[`answer${index + 1}`] || ""}
                                disabled={isLoading}
                            />
                            <Radio
                                className="mt-2"
                                id={`answer${index + 1}-radio`}
                                name="correctAnswer"
                                checked={answer.is_correct}
                                label={`Պատասխան ${index + 1}-ը ճիշտ է`}
                                onChange={() => handleCorrectAnswerChange(index)}
                                disabled={isLoading}
                            />
                            {index > 1 && (
                                <button
                                    type="button"
                                    onClick={() => removeAnswer(index)}
                                    className="mt-1 text-xs text-yellow-500 hover:underline"
                                    disabled={isLoading}
                                >
                                    Հեռացնել պատասխանը
                                </button>
                            )}
                        </div>
                    ))}
                </div>

                {errorMessages.correctAnswer && (
                    <p className="text-sm text-red-600 mt-2">{errorMessages.correctAnswer}</p>
                )}

                <div className="mt-4">
                    <button
                        type="button"
                        onClick={addAnswer}
                        className="px-4 py-2 text-white text-sm bg-green-600 hover:bg-green-700 rounded"
                        disabled={isLoading}
                    >
                        Ավելացնել պատասխան
                    </button>
                </div>
            </ComponentCard>

            <div className="flex justify-end">
                <button
                    type="submit"
                    className="px-5 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors disabled:bg-gray-400"
                    disabled={isLoading}
                >
                    {isEditMode ? "Խմբագրել" : "Ավելացնել"}
                </button>
            </div>
        </form>
    );
}
