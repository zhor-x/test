import ComponentCard from "@/components/common/ComponentCard.jsx";
import Input from "@/components/common/InputField.jsx";
import Label from "@/components/common/Label.jsx";
import SignGroupsAPI from "@/services/api/SignGroupsAPI.js";
import {useEffect, useState} from "react";
import {useNavigate, useParams} from "react-router-dom";

export default function AddEdit() {
    const {id} = useParams();
    const isEditMode = Boolean(id);

    const navigate = useNavigate();
    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");
    const [errorMessages, setErrorMessages] = useState({});
    const [successMessage, setSuccessMessage] = useState("");

    useEffect(() => {
        if (isEditMode) {
            SignGroupsAPI.getById(id)
                .then((res) => {
                    const data = res?.data;
                    if (data) {
                        setTitle(data.title || "");
                        setDescription(data.description || "");
                    }
                })
                .catch((err) => {
                    console.error("Failed to fetch category", err);
                    navigate("/road-sign-groups");
                });
        }
    }, [isEditMode, id, navigate]);


    const validateForm = () => {
        const newErrorMessages = {};
        let valid = true;

        if (!title.trim()) {
            valid = false;

            newErrorMessages.title = "Խմբի անվանումը պարտադիր է։"


        }
        if (!description.trim()) {
            valid = false;
            newErrorMessages.description = "Նկարագրության անվանումը պարտադիր է։"
        }

        setErrorMessages(newErrorMessages);
        return valid;
    };


    const handleSubmit = async (e) => {
        e.preventDefault();
        setSuccessMessage("");
        setErrorMessages({});

        if (!validateForm()) return;

        try {
            let response;
            const payload = {title, description};

            if (isEditMode) {
                response = await SignGroupsAPI.update({id, ...payload});
            } else {
                response = await SignGroupsAPI.create(payload);
            }

            if (response?.status === 200 || response?.status === 201) {
                const successMsg = isEditMode
                    ? "Խմբագրումն իրականացվեց հաջողությամբ։"
                    : "Խումբը հաջողությամբ ավելացվեց։";

                navigate("/road-sign-groups", {
                    state: {successMessage: successMsg},
                });

                setSuccessMessage(successMsg);
                if (!isEditMode) {
                    setTitle("");
                    setDescription("");
                }
            }
        } catch (err) {
            console.error(err);
            if (err?.response?.data?.errors) {
                setErrorMessages(err.response.data.errors);
            } else {
                setErrorMessages({general: "Սխալ է տեղի ունեցել։"});
            }
        }
    };

    return (
        <ComponentCard title={isEditMode ? "Խմբագրել տեսական թեստերի խումբ" : "Ավելացնել տեսական թեստերի խումբ"}>
            <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                    <Label htmlFor="title">Խումբ</Label>
                    <Input
                        type="text"
                        id="title"
                        name="title"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        placeholder="Խումբ 1"
                        error={errorMessages.title}
                        hint={errorMessages.title || ""}
                    />
                </div>

                <div>
                    <Label htmlFor="description">Նկարագրություն</Label>
                    <Input
                        type="text"
                        id="description"
                        name="description"
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                        placeholder="Նկարագրություն"
                        error={errorMessages.description}
                        hint={errorMessages.description || ""}
                    />
                </div>

                {successMessage && (
                    <p className="text-sm text-green-600 dark:text-green-400">{successMessage}</p>
                )}

                {errorMessages.general && (
                    <p className="text-sm text-red-600 dark:text-red-400">{errorMessages.general}</p>
                )}

                <div className="flex justify-end">
                    <button
                        type="submit"
                        className="px-5 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors"
                    >
                        {isEditMode ? "Խմբագրել" : "Ավելացնել"}
                    </button>
                </div>
            </form>
        </ComponentCard>
    );
}
