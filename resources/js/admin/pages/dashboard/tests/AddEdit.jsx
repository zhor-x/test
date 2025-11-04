import ComponentCard from "@/components/common/ComponentCard.jsx";
import Input from "@/components/common/InputField.jsx";
import Label from "@/components/common/Label.jsx";
import React, {useCallback, useEffect, useState} from "react";
import {useNavigate, useParams} from "react-router-dom";
import Radio from "@/components/common/Radio.jsx";
import QuestionAPI from "@/services/api/QuestionAPI.js";
import {DragDropContext, Draggable, Droppable} from "react-beautiful-dnd";
import TestAPI from "@/services/api/TestAPI.js";

// Custom debounce hook
const useDebounce = (value, delay) => {
    const [debouncedValue, setDebouncedValue] = useState(value);

    useEffect(() => {
        const handler = setTimeout(() => {
            setDebouncedValue(value);
        }, delay);

        return () => {
            clearTimeout(handler);
        };
    }, [value, delay]);

    return debouncedValue;
};

export default function AddEdit() {
    const {id} = useParams();
    const isEditMode = Boolean(id);
    const navigate = useNavigate();

    // State management
    const [title, setTitle] = useState("");
    const [duration, setDuration] = useState(30);
    const [maxWrongAnswers, setMaxWrongAnswers] = useState(2);
    const [hidden, setHidden] = useState(true);
    const [selectedQuestions, setSelectedQuestions] = useState([]);
    const [errorMessages, setErrorMessages] = useState({});
    const [successMessage, setSuccessMessage] = useState("");
    const [questions, setQuestions] = useState([]);
    const [allQuestions, setAllQuestions] = useState([]);

    const [searchTerm, setSearchTerm] = useState("");
    const [currentPage, setCurrentPage] = useState(1);
    const [totalQuestions, setTotalQuestions] = useState(0);
    const [isLoading, setIsLoading] = useState(false);
    const questionsPerPage = 10;

    // Debounce search term
    const debouncedSearchTerm = useDebounce(searchTerm, 500);

    // Fetch questions with pagination and debounced search
    const getQuestions = useCallback(async () => {
        try {
            setIsLoading(true);
            const resp = await QuestionAPI.getList(currentPage, {
                search: debouncedSearchTerm,
                limit: questionsPerPage,
            });
            setQuestions(resp.data || []);
            setAllQuestions((prev) => {
                const newQuestions = [...prev, ...(resp.data || [])];
                return Array.from(new Map(newQuestions.map((q) => [q.id, q])).values());
            });
            setTotalQuestions(resp?.meta?.total || 0);
        } catch (error) {
            console.error("Failed to fetch questions:", error);
            setErrorMessages({general: "Հարցերի բեռնումը ձախողվեց։"});
        } finally {
            setIsLoading(false);
        }
    }, [currentPage, debouncedSearchTerm]);

    // Fetch quiz data in edit mode
    const fetchTest = useCallback(async () => {
        if (!isEditMode) return;
        setIsLoading(true);
        try {
            const res = await TestAPI.getTest(id);
            const {title, duration, max_wrong_answers, hidden, questions} = res.data;
            setTitle(title || "");
            setDuration(duration || "");
            setMaxWrongAnswers(max_wrong_answers || "");
            setHidden(hidden || false);
            if (questions) {
                setAllQuestions(questions);
                setSelectedQuestions(questions.map((q) => q.id) || [])
            }
            // setSelectedQuestions(questions.map((q) => q.id) || []);
        } catch (err) {
            console.error("Failed to fetch quiz:", err);
            navigate("/tests", {state: {errorMessage: "Հարցաշարը չի գտնվել։"}});
        } finally {
            setIsLoading(false);
        }
    }, [id, isEditMode, navigate]);

    useEffect(() => {
        getQuestions();
        fetchTest();
    }, [getQuestions, fetchTest]);

    // Form validation
    const validateForm = () => {
        const newErrorMessages = {};
        let valid = true;

        if (!title.trim()) {
            valid = false;
            newErrorMessages.title = "Վերնագիրը պարտադիր է։";
        }

        if (!duration || isNaN(duration) || duration <= 0) {
            valid = false;
            newErrorMessages.duration = "Տևողությունը պետք է լինի վավեր թիվ։";
        }

        if (!maxWrongAnswers || isNaN(maxWrongAnswers) || maxWrongAnswers < 0) {
            valid = false;
            newErrorMessages.maxWrongAnswers = "Սխալ պատասխանների առավելագույն թիվը պետք է լինի վավեր։";
        }

        if (selectedQuestions.length === 0) {
            valid = false;
            newErrorMessages.questions = "Խնդրում ենք ընտրել առնվազն մեկ հարց։";
        }

        if (selectedQuestions.length > 20) {
            valid = false;
            newErrorMessages.questions = "Հնարավոր է ընտրել առավելագույնը 20 հարց։";
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
            formData.append("title", title);
            formData.append("duration", duration);
            formData.append("max_wrong_answers", maxWrongAnswers);
            formData.append("hidden", hidden ? 1 : 0);
            selectedQuestions.forEach((questionId, index) => {
                formData.append(`questions[${index}]`, questionId);
            });

            if (isEditMode) {
                var putData = {
                    title,
                    duration,
                    max_wrong_answers: maxWrongAnswers,
                    hidden: hidden ? 1 : 0,
                    test_id: id,
                    questions: selectedQuestions,
                };

            }


            const response = isEditMode
                ? await TestAPI.updateTest(id, putData)
                : await TestAPI.createTest(formData);

            if ([200, 201].includes(response?.status)) {
                navigate("/tests", {
                    state: {
                        successMessage: isEditMode
                            ? "Հարցաշարը հաջողությամբ խմբագրվեց։"
                            : "Հարցաշարը հաջողությամբ ավելացվեց։",
                    },
                });

                if (!isEditMode) {
                    setTitle("");
                    setDuration("");
                    setMaxWrongAnswers("");
                    setHidden(false);
                    setSelectedQuestions([]);
                }
            }
        } catch (err) {
            console.error("Submission error:", err);
            setErrorMessages({
                general: err.message || "Ինչ-որ բան սխալ է։ Խնդրում ենք կրկին փորձել։",
            });
        } finally {
            setIsLoading(false);
        }
    };

    // Handle question selection
    const handleQuestionSelect = (questionId) => {
        if (selectedQuestions.includes(questionId)) {
            setSelectedQuestions(selectedQuestions.filter((id) => id !== questionId));
        } else if (selectedQuestions.length < 20) {
            setSelectedQuestions([...selectedQuestions, questionId]);
        }
    };

    // Handle drag-and-drop
    const handleDragEnd = (result) => {
        if (!result.destination) return; // Dropped outside the list

        const newOrder = [...selectedQuestions];
        const [reorderedItem] = newOrder.splice(result.source.index, 1);
        newOrder.splice(result.destination.index, 0, reorderedItem);
        setSelectedQuestions(newOrder);
    };

    // Handle search
    const handleSearch = (e) => {
        setSearchTerm(e.target.value);
        setCurrentPage(1); // Reset to first page on search
    };

    // Handle pagination
    const totalPages = Math.ceil(totalQuestions / questionsPerPage);
    const handlePageChange = (page) => {
        if (page >= 1 && page <= totalPages) {
            setCurrentPage(page);
        }
    };


    // Get question text by ID for selected questions list
    const getQuestionText = (id) => {
        const question = allQuestions.find((q) => q.id === id);
        return question ? question.text || question.title : "Unknown Question";
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <ComponentCard title={isEditMode ? "Խմբագրել հարցաշար" : "Ավելացնել հարցաշար"}>
                {isLoading && <p>Բեռնվում է...</p>}
                <div>
                    <Label htmlFor="title">Վերնագիր</Label>
                    <Input
                        id="title"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        placeholder="Հարցաշարի վերնագիր"
                        error={errorMessages.title}
                        hint={errorMessages.title || ""}
                        disabled={isLoading}
                    />
                </div>

                <div>
                    <Label htmlFor="duration">Տևողություն (րոպե)</Label>
                    <Input
                        id="duration"
                        type="number"
                        value={duration}
                        onChange={(e) => setDuration(e.target.value)}
                        placeholder="Տևողություն"
                        error={errorMessages.duration}
                        hint={errorMessages.duration || ""}
                        disabled={isLoading}
                    />
                </div>

                <div>
                    <Label htmlFor="maxWrongAnswers">Սխալ պատասխանների առավելագույն թիվ</Label>
                    <Input
                        id="maxWrongAnswers"
                        type="number"
                        value={maxWrongAnswers}
                        onChange={(e) => setMaxWrongAnswers(e.target.value)}
                        placeholder="Սխալ պատասխանների թիվ"
                        error={errorMessages.maxWrongAnswers}
                        hint={errorMessages.maxWrongAnswers || ""}
                        disabled={isLoading}
                    />
                </div>

                <div>
                    <Label>Ցուցադրել</Label>
                    <div className="flex space-x-4">
                        <Radio
                            id="hidden-no"
                            name="hidden"
                            checked={hidden}
                            label="Ոչ"
                            onChange={() => setHidden(true)}
                            disabled={isLoading}
                        />
                        <Radio
                            id="hidden-yes"
                            name="hidden"
                            checked={!hidden}
                            label="Այո"
                            onChange={() => setHidden(false)}
                            disabled={isLoading}
                        />
                    </div>
                </div>

                <div>
                    <Label htmlFor="questions">Հարցեր (ընտրեք մինչև 20 հարց)</Label>
                    <div className="space-y-4">
                        {/* Selected Questions */}
                        {selectedQuestions.length > 0 && (
                            <DragDropContext onDragEnd={handleDragEnd}>
                                <Droppable droppableId="selectedQuestions">
                                    {(provided) => (
                                        <div
                                            className="border border-gray-300 rounded-lg p-4 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 shadow-sm mt-4 mb-4"
                                            {...provided.droppableProps}
                                            ref={provided.innerRef}
                                        >
                                            <h4 className="text-base font-medium text-gray-700 dark:text-gray-300 mb-3">
                                                Ընտրված հարցեր ({selectedQuestions.length}/20)
                                            </h4>
                                            <ul className="space-y-2">
                                                {selectedQuestions.map((questionId, index) => (
                                                    <Draggable
                                                        key={questionId}
                                                        draggableId={questionId.toString()}
                                                        index={index}
                                                        isDragDisabled={isLoading}
                                                    >
                                                        {(provided) => (
                                                            <li
                                                                ref={provided.innerRef}
                                                                {...provided.draggableProps}
                                                                className="flex justify-between items-center whitespace-normal text-base text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-900 rounded-md p-3 hover:bg-gray-100 dark:hover:bg-gray-800 transition shadow-sm"
                                                            >
                                                                <div className="flex items-center space-x-3 flex-1">
                                                                    <div
                                                                        {...provided.dragHandleProps}
                                                                        className="text-gray-500 dark:text-gray-400 cursor-move"
                                                                    >
                                                                        <svg
                                                                            className="w-6 h-6"
                                                                            fill="none"
                                                                            stroke="currentColor"
                                                                            viewBox="0 0 24 24"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                        >
                                                                            <path
                                                                                strokeLinecap="round"
                                                                                strokeLinejoin="round"
                                                                                strokeWidth="2"
                                                                                d="M4 8h16M4 16h16"
                                                                            />
                                                                        </svg>
                                                                    </div>
                                                                    <span>{index + 1}. {getQuestionText(questionId)}</span>
                                                                </div>
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleQuestionSelect(questionId)}
                                                                    className="text-red-500 hover:text-red-700 text-sm font-medium"
                                                                    disabled={isLoading}
                                                                >
                                                                    Հեռացնել
                                                                </button>
                                                            </li>
                                                        )}
                                                    </Draggable>
                                                ))}
                                                {provided.placeholder}
                                            </ul>
                                        </div>
                                    )}
                                </Droppable>
                            </DragDropContext>
                        )}

                        {/* Search Input */}
                        <Input
                            id="searchQuestions"
                            type="text"
                            value={searchTerm}
                            onChange={handleSearch}
                            placeholder="Որոնել հարցեր..."
                            disabled={isLoading}
                            className="w-full"
                        />

                        {/* Questions Table */}
                        <div className="border rounded-lg overflow-hidden">
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead className="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Ընտրել
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Նկար
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Հարց
                                    </th>
                                </tr>
                                </thead>
                                <tbody
                                    className="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                {questions.length > 0 ? (
                                    questions.map((question) => (
                                        <tr key={question.id}>
                                            <td className="px-6 py-4  ">
                                                <input
                                                    type="checkbox"
                                                    checked={selectedQuestions.includes(question.id)}
                                                    onChange={() => handleQuestionSelect(question.id)}
                                                    disabled={
                                                        isLoading ||
                                                        (selectedQuestions.length >= 20 &&
                                                            !selectedQuestions.includes(question.id))
                                                    }
                                                    className="h-4 w-4 text-brand-500 focus:ring-brand-500 border-gray-300 rounded"
                                                />
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                                {question.image && (
                                                    <img
                                                        src={question.image}
                                                        alt="Նկար"
                                                        className="w-16 h-16 object-contain border rounded"
                                                    />
                                                )}
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                                {question.text || question.title}
                                                <br/>
                                                <div className="text-xs text-gray-500 dark:text-gray-400">
                                                    {question.answers.map((answer, index) => (
                                                        <React.Fragment key={index}>
                                                            {answer.translation.title}
                                                            {index < question.answers.length - 1 && <br/>}
                                                        </React.Fragment>
                                                    ))}
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td
                                            colSpan={2}
                                            className="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            Հարցեր չեն գտնվել
                                        </td>
                                    </tr>
                                )}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        {totalPages > 1 && (
                            <div className="flex justify-between items-center mt-4">
                                <button
                                    type="button"
                                    onClick={() => handlePageChange(currentPage - 1)}
                                    disabled={currentPage === 1 || isLoading}
                                    className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    Նախորդ
                                </button>
                                <span className="text-sm text-gray-700 dark:text-gray-300">
                  Էջ {currentPage} / {totalPages}
                </span>
                                <button
                                    type="button"
                                    onClick={() => handlePageChange(currentPage + 1)}
                                    disabled={currentPage === totalPages || isLoading}
                                    className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    Հաջորդ
                                </button>
                            </div>
                        )}

                        {/* Error and Count */}
                        {errorMessages.questions && (
                            <p className="text-sm text-red-600 mt-2">{errorMessages.questions}</p>
                        )}
                        <p className="text-sm mt-2">
                            Ընտրված հարցեր: {selectedQuestions.length}/20
                        </p>
                    </div>
                </div>

                {successMessage && (
                    <p className="text-sm text-green-600 dark:text-green-400">{successMessage}</p>
                )}
                {errorMessages.general && (
                    <p className="text-sm text-red-600 dark:text-red-400">{errorMessages.general}</p>
                )}
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
