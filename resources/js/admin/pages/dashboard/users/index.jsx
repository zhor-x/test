import ComponentCard from "@/components/common/ComponentCard.jsx";
import Table from "@/components/common/Table.jsx";
import React, {useCallback, useEffect, useState} from "react";
import {useNavigate} from "react-router-dom";
import {useLocation} from "react-router";
import {SuccessNotification} from "@/components/common/SuccessNotification.jsx";
import QuestionAPI from "@/services/api/QuestionAPI.js";
import DeleteModal from "@/components/modals/DeleteModal.jsx";
import TestAPI from "@/services/api/TestAPI.js";
import UserApi from "@/services/api/UserAPI.js";
import Input from "@/components/common/InputField.jsx";


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


export default function Users() {
    const [deletedModalData, setDeletedModalData] = useState(null);

    const location = useLocation();
    const [showSuccess, setShowSuccess] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");
    const [users, setUsers] = useState([]);
    const [meta, setMeta] = useState([]);
    const [isModalOpen, setModalOpen] = useState(false);
    const [currentPage, setCurrentPage] = useState(1);
    const [searchTerm, setSearchTerm] = useState("");
     const debouncedSearchTerm = useDebounce(searchTerm, 500);
    const [isLoading, setIsLoading] = useState(false);


    useEffect(() => {
        if (location.state?.successMessage) {
            setSuccessMessage(location.state.successMessage);
            setShowSuccess(true);
            window.history.replaceState({}, document.title); // Clear state
        }
    }, [location.state]);

    const navigator = useNavigate();

    const titles = [
        {title: 'ID'},
        {title: 'Անուն'},
        {title: 'Էլ. Հասցե'},
        {title: 'Հեռ.'},
        {title: 'Խումբ'},
    ];


    const getList = useCallback(async (page) => {
        try {
            setIsLoading(true);
            const resp = await UserApi.getList(currentPage, {
                search: debouncedSearchTerm,
                limit: 10,
            });
            setMeta(resp.meta);
             setUsers(resp.data);

          } catch (error) {
            console.error("Failed to fetch questions:", error);
        } finally {
            setIsLoading(false);
        }
    }, [currentPage, debouncedSearchTerm])


    const deleteItem = (item) => {
        setDeletedModalData({
            id: item.id,
            title: item.name
        })

        setModalOpen(true)
    }

    useEffect(() => {
        getList()
    }, [getList]);

    const handleSearch = (e) => {
        setSearchTerm(e.target.value);
        setCurrentPage(1); // Reset to first page on search
    };


    const handleDelete = async (id) => {
        setModalOpen(false)
        await UserApi.deleteUser(id);
        setSuccessMessage('Հարցը հաջողությամբ ջնջված է');
        setUsers((prevUsers) =>
            prevUsers.filter((user) => user.id !== id)
        );
        setShowSuccess(true)
    }

    return (
        <ComponentCard newButton={{title: 'Նոր օգտատեր', link: '/users/new'}} title="Օգտատերեր">
            {showSuccess && (
                <SuccessNotification
                    message={successMessage}
                    onClose={() => setShowSuccess(false)}
                />
            )}

            <DeleteModal
                isOpen={isModalOpen}
                data={deletedModalData}
                onClose={() => setModalOpen(false)}
                onDelete={handleDelete}
            />
            <Input
                id="searchQuestions"
                type="text"
                value={searchTerm}
                onChange={handleSearch}
                placeholder="Որոնել հարցեր..."
                disabled={isLoading}
                className="w-full"
            />
            <Table
                meta={meta}
                titles={titles}
                data={users}
                results={(id)=> navigator(`/users/${id}/results`)}
                goToEdit={(id) => navigator(`/users/${id}`)}
                deleteItem={deleteItem}
                onPageChange={(page) => setCurrentPage(page)}
            />
        </ComponentCard>
    );
}
