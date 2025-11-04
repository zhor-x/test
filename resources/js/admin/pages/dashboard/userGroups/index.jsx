import ComponentCard from "@/components/common/ComponentCard.jsx";
import Table from "@/components/common/Table.jsx";
import React, {useCallback, useEffect, useState} from "react";
import {useNavigate} from "react-router-dom";
import {useLocation} from "react-router";
import {SuccessNotification} from "@/components/common/SuccessNotification.jsx";
import DeleteModal from "@/components/modals/DeleteModal.jsx";
import UserGroupApi from "@/services/api/UserGroupAPI.js";
import Input from "@/components/common/InputField.jsx";
import userGroupAPI from "@/services/api/UserGroupAPI.js";


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
export default function UsersGroups() {
    const [deletedModalData, setDeletedModalData] = useState(null);

    const location = useLocation();
    const [showSuccess, setShowSuccess] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");
    const [userGroups, setUserGroups] = useState([]);
    const [meta, setMeta] = useState([]);
    const [isModalOpen, setModalOpen] = useState(false);
    const [currentPage, setCurrentPage] = useState(1);
    const [searchTerm, setSearchTerm] = useState("");
    const [errorMessages, setErrorMessages] = useState({});
    const [isLoading, setIsLoading] = useState(false);
    const debouncedSearchTerm = useDebounce(searchTerm, 500);


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
        {title: 'Օգտատերերի քանակ'},
        {title: 'Ստեղծման ամսաթիվ'},
    ];


    const getGoups = useCallback(async () => {
        try {
            setIsLoading(true);
            const resp = await UserGroupApi.getList(currentPage, {
                search: debouncedSearchTerm,
                limit: 10,
            });
            setMeta(resp.meta);
            setUserGroups(resp.data)

            setUserGroups(resp.data.map((item) => ({
                id: item.id,
                title: item.title,
                users_count: item.users_count ?? 0,
                created_at: item.created_at,
            })));
        } catch (error) {
            console.error("Failed to fetch questions:", error);
        } finally {
            setIsLoading(false);
        }
    }, [currentPage, debouncedSearchTerm])


    useEffect(() => {
        getGoups()
    }, [getGoups]);

    const deleteItem = (item) => {
        setDeletedModalData({
            id: item.id,
            title: item.name
        })

        setModalOpen(true)
    }


    const handleDelete = async (id) => {
        setModalOpen(false)
        await userGroupAPI.destroy(id);
        setSuccessMessage('Հարցը հաջողությամբ ջնջված է');
        setUserGroups((prevUsers) =>
            prevUsers.filter((user) => user.id !== id)
        );
        setShowSuccess(true)
    }

    const handleSearch = (e) => {
        setSearchTerm(e.target.value);
        setCurrentPage(1); // Reset to first page on search
    };

    return (
        <ComponentCard newButton={{title: 'Նոր խումբ', link: '/user-groups/new'}} title="Խմբեր">
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
                placeholder="Որոնել խմբեր..."
                disabled={isLoading}
                className="w-full"
            />
            <Table
                meta={meta}
                titles={titles}
                data={userGroups}
                results={(id) => navigator(`/user-groups/${id}/results`)}
                goToEdit={(id) => navigator(`/user-groups/${id}`)}
                deleteItem={deleteItem}
                onPageChange={(page) => setCurrentPage(page)}
            />
        </ComponentCard>
    );
}
