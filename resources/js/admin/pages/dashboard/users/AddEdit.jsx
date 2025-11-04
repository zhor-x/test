import ComponentCard from "@/components/common/ComponentCard.jsx";
import Input from "@/components/common/InputField.jsx";
import Label from "@/components/common/Label.jsx";
import React, {useCallback, useEffect, useState} from "react";
import {useNavigate, useParams} from "react-router-dom";
import Radio from "@/components/common/Radio.jsx";
import QuestionAPI from "@/services/api/QuestionAPI.js";
import {DragDropContext, Draggable, Droppable} from "react-beautiful-dnd";
import TestAPI from "@/services/api/TestAPI.js";
import GroupsAPI from "@/services/api/GroupsAPI.js";
import UserApi from "@/services/api/UserAPI.js";

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
    const [name, setName] = useState("");
    const [email, setEmail] = useState("");
     const [phone, setPhone] = useState("");
     const [errorMessages, setErrorMessages] = useState({});
    const [successMessage, setSuccessMessage] = useState("");

      const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        if (isEditMode) {
            UserApi.getUser(id)
                .then((res) => {
                    const data = res?.data;
                    if (data) {
                        setName(data.name || "");
                        setEmail(data.email || "");
                        setPhone(data.phone || "");
                    }
                })
                .catch((err) => {
                    console.error("Failed to fetch category", err);
                    navigate("/users");
                });
        }
    }, [isEditMode, id, navigate]);


    const validateForm = () => {
        const newErrorMessages = {};
        let valid = true;

        if (!name.trim()) {
            valid = false;

            newErrorMessages.title = "Օգտատիրոջ անունը պարտադիր է։"


        }
        if (!email.trim()) {
            valid = false;
            newErrorMessages.description = "Օգտատիրոջ էլ. հասցեն պարտադիր է։"
        }


        if (!phone.trim()) {
            valid = false;
            newErrorMessages.description = "Օգտատիրոջ հեռախոսահամարը պարտադիր է։"
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
            const payload = {name, phone, email};

            if (isEditMode) {
                 response = await UserApi.update(id, payload);
            } else {
                response = await UserApi.create(payload);
            }

            if (response?.status === 200 || response?.status === 201) {
                const successMsg = isEditMode
                    ? "Խմբագրումն իրականացվեց հաջողությամբ։"
                    : "Օգտատերը հաջողությամբ ավելացվեց։";

                navigate("/users", {
                    state: {successMessage: successMsg},
                });

                setSuccessMessage(successMsg);
                if (!isEditMode) {
                    setPhone("");
                    setEmail("");
                    setName("");
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
        <form onSubmit={handleSubmit} className="space-y-4">
            <ComponentCard title={isEditMode ? "Խմբագրել օգտատիրոջը" : "Ավելացնել օգտատիրոջը"}>
                {isLoading && <p>Բեռնվում է...</p>}
                <div>
                    <Label htmlFor="title">Անուն</Label>
                    <Input
                        id="name"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        placeholder="Անուն Ազգանուն"
                        error={errorMessages.name}
                        hint={errorMessages.name || ""}
                        disabled={isLoading}
                    />
                </div>

                <div>
                    <Label htmlFor="duration">Էլ. հասցե</Label>
                    <Input
                        id="email"
                         value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="Էլ. հասցե"
                        error={errorMessages.email}
                        hint={errorMessages.email || ""}
                        disabled={isLoading}
                    />
                </div>

                <div>
                    <Label htmlFor="duration">Հեռախոս</Label>
                    <Input
                        id="phone"
                         value={phone}
                        onChange={(e) => setPhone(e.target.value)}
                        placeholder="Հեռախոս"
                        error={errorMessages.phone}
                        hint={errorMessages.phone || ""}
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
