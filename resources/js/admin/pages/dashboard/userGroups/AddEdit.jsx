import ComponentCard from "@/components/common/ComponentCard.jsx";
import Input from "@/components/common/InputField.jsx";
import Label from "@/components/common/Label.jsx";
import React, {useCallback, useEffect, useState} from "react";
import {useNavigate, useParams} from "react-router-dom";
import Radio from "@/components/common/Radio.jsx";
import QuestionAPI from "@/services/api/QuestionAPI.js";
import {DragDropContext, Draggable, Droppable} from "react-beautiful-dnd";
import TestAPI from "@/services/api/TestAPI.js";
import userGroupAPI from "@/services/api/UserGroupAPI.js";
import UserGroupApi from "@/services/api/UserGroupAPI.js";

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
    const [selectedUsers, setSelectedUsers] = useState([]);
    const [title, setTitle] = useState("");
    const [errorMessages, setErrorMessages] = useState({});
    const [successMessage, setSuccessMessage] = useState("");
    const [users, setUsers] = useState([]);
    const [allUser, setAllUser] = useState([]);

    const [searchTerm, setSearchTerm] = useState("");
    const [currentPage, setCurrentPage] = useState(1);
    const [totalUsers, setTotalUsers] = useState(0);
    const [isLoading, setIsLoading] = useState(false);
    const usersPerPage = 10;

    // Debounce search term
    const debouncedSearchTerm = useDebounce(searchTerm, 500);

    // Fetch users with pagination and debounced search
    const getUsers = useCallback(async () => {
        try {
            setIsLoading(true);
            const resp = await UserGroupApi.getUsers(currentPage, {
                search: debouncedSearchTerm,
                limit: usersPerPage,
            });
            setUsers(resp.data || []);
            setAllUser((prev) => {
                const newUsers = [...prev, ...(resp.data || [])];
                return Array.from(new Map(newUsers.map((q) => [q.id, q])).values());
            });
            setTotalUsers(resp?.meta?.total || 0);
        } catch (error) {
            console.error("Failed to fetch users:", error);
            setErrorMessages({general: "Օգտատերերի բեռնումը ձախողվեց։"});
        } finally {
            setIsLoading(false);
        }
    }, [currentPage, debouncedSearchTerm]);

    // Fetch quiz data in edit mode
    const fetchGroup = useCallback(async () => {
        if (!isEditMode) return;
        setIsLoading(true);
        try {
            const res = await userGroupAPI.getUser(id);
            const {title, users} = res.data;
            setTitle(title || "");
             if (users) {
                setAllUser(users);
                setSelectedUsers(users.map((q) => q.id) || [])
            }
        } catch (err) {
            console.error("Failed to fetch quiz:", err);
            navigate("/user-groups", {state: {errorMessage: "Խումբ չի գտնվել։"}});
        } finally {
            setIsLoading(false);
        }
    }, [id, isEditMode, navigate]);

    useEffect(() => {
        getUsers();
        fetchGroup();
    }, [getUsers, fetchGroup]);

    // Form validation
    const validateForm = () => {
        const newErrorMessages = {};
        let valid = true;

        if (!title.trim()) {
            valid = false;
            newErrorMessages.title = "Խբմի անունը պարտադիր է։";
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
            selectedUsers.forEach((userId, index) => {
                formData.append(`users[${index}]`, userId);
            });

            if (isEditMode) {
                var putData = {
                    title,
                    users: selectedUsers,
                };
            }

            const response = isEditMode
                ? await UserGroupApi.update(id, putData)
                : await UserGroupApi.create(formData);

            if ([200, 201].includes(response?.status)) {
                navigate("/user-groups", {
                    state: {
                        successMessage: isEditMode
                            ? "Խմբագրումն իրականացվեց հաջողությամբ։"
                            : "Խումբը հաջողությամբ ավելացվեց։",
                    },
                });

                if (!isEditMode) {
                    setTitle("");
                    setSelectedUsers([]);
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
    const handleUserSelect = (userId) => {
        if (selectedUsers.includes(userId)) {
            setSelectedUsers(selectedUsers.filter((id) => id !== userId));
        } else if (selectedUsers.length < 20) {
            setSelectedUsers([...selectedUsers, userId]);
        }
    };

    // Handle drag-and-drop
    const handleDragEnd = (result) => {
        if (!result.destination) return; // Dropped outside the list

        const newOrder = [...selectedUsers];
        const [reorderedItem] = newOrder.splice(result.source.index, 1);
        newOrder.splice(result.destination.index, 0, reorderedItem);
        setSelectedUsers(newOrder);
    };

    // Handle search
    const handleSearch = (e) => {
        setSearchTerm(e.target.value);
        setCurrentPage(1); // Reset to first page on search
    };

    // Handle pagination
    const totalPages = Math.ceil(totalUsers / usersPerPage);
    const handlePageChange = (page) => {
        if (page >= 1 && page <= totalPages) {
            setCurrentPage(page);
        }
    };


    // Get question text by ID for selected users list
    const getUserName = (id) => {
        const user = allUser.find((q) => q.id === id);
        return user ?` ${user.name}   ${user.email}   ${user.phone}` : "Unknown User";
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <ComponentCard title={isEditMode ? "Խմբագրել Խումբ" : "Ավելացնել Խումբ"}>
                {isLoading && <p>Բեռնվում է...</p>}
                <div>
                    <Label htmlFor="title">Խմբի անուն</Label>
                    <Input
                        id="title"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        placeholder="Խմբի անուն"
                        error={errorMessages.title}
                        hint={errorMessages.title || ""}
                        disabled={isLoading}
                    />
                </div>






                <div>
                    <Label htmlFor="questions">Հարցեր (ընտրեք մինչև 20 հարց)</Label>
                    <div className="space-y-4">
                        {/* Selected Questions */}
                        {selectedUsers.length > 0 && (
                            <DragDropContext onDragEnd={handleDragEnd}>
                                <Droppable droppableId="selectedQuestions">
                                    {(provided) => (
                                        <div
                                            className="border border-gray-300 rounded-lg p-4 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 shadow-sm mt-4 mb-4"
                                            {...provided.droppableProps}
                                            ref={provided.innerRef}
                                        >
                                            <h4 className="text-base font-medium text-gray-700 dark:text-gray-300 mb-3">
                                                Ընտրված հարցեր ({selectedUsers.length}/20)
                                            </h4>
                                            <ul className="space-y-2">
                                                {selectedUsers.map((userId, index) => (
                                                    <Draggable
                                                        key={userId}
                                                        draggableId={userId.toString()}
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
                                                                    <span>{index + 1}. {getUserName(userId)}</span>
                                                                </div>
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleUserSelect(userId)}
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

                         <Input
                            id="searchQuestions"
                            type="text"
                            value={searchTerm}
                            onChange={handleSearch}
                            placeholder="Որոնել օգտատեր..."
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
                                        Անուն
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Էլ. հասցե
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Հեռ
                                    </th>
                                </tr>
                                </thead>
                                <tbody
                                    className="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                {users.length > 0 ? (
                                    users.map((user) => (
                                        <tr key={user.id}>
                                            <td className="px-6 py-4  ">
                                                <input
                                                    type="checkbox"
                                                    checked={selectedUsers.includes(user.id)}
                                                    onChange={() => handleUserSelect(user.id)}
                                                    disabled={
                                                        isLoading ||
                                                        (selectedUsers.length >= 20 &&
                                                            !selectedUsers.includes(user.id))
                                                    }
                                                    className="h-4 w-4 text-brand-500 focus:ring-brand-500 border-gray-300 rounded"
                                                />
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                                {user.name}
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                                {user.email }
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                                {user.phone}
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td
                                            colSpan={4}
                                            className="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            Օգտատերեր չեն գտնվել
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

                         {errorMessages.questions && (
                            <p className="text-sm text-red-600 mt-2">{errorMessages.questions}</p>
                        )}

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
