import ComponentCard from "@/components/common/ComponentCard.jsx";
import Table from "@/components/common/Table.jsx";
import {useEffect, useState} from "react";
import GroupsAPI from "@/services/api/GroupsAPI.js";
import {useNavigate} from "react-router-dom";
import {useLocation} from "react-router";
import {SuccessNotification} from "@/components/common/SuccessNotification.jsx";
import DeleteModal from "@/components/modals/DeleteModal.jsx";

export default function Groups() {
    const [isModalOpen, setModalOpen] = useState(false);
    const [deletedModalData, setDeletedModalData] = useState(null);

    const location = useLocation();
    const [showSuccess, setShowSuccess] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");

    useEffect(() => {
        if (location.state?.successMessage) {
            setSuccessMessage(location.state.successMessage);
            setShowSuccess(true);
            window.history.replaceState({}, document.title); // Clear state
        }
    }, [location.state]);

    const navigator = useNavigate();
    const [categories, setCategories] = useState([]);
    const titles = [
        {title: 'ID'},
        {title: 'Խումբ'},
        {title: 'Նկարագրություն'},
    ];


    const getCategories = async () => {
        // setLoading(true);
        try {
            const resp = await GroupsAPI.getCategoryList();
             setCategories(resp.groups);
        } catch (error) {
            console.log(error);

        } finally {
            // setLoading(false);
        }

    }

    useEffect(() => {
        getCategories()
    }, []);

    const deleteItem = (item) => {
        setDeletedModalData({
            id: item.id,
            title: item.title
        })


        setModalOpen(true)
    }


    const handleDelete = async (id) => {
        setModalOpen(false)
        await GroupsAPI.deleteCategory(id);
        setSuccessMessage('Տեսական թեստերի խումբը հաջողությամբ ջնջված է');
        setCategories((prevCategories) =>
            prevCategories.filter((category) => category.id !== id)
        );
        setShowSuccess(true)
    }


    return (
        <ComponentCard newButton={{title: 'Նոր Խումբ', link: '/groups/new'}} title="Տեսական թեստերի խմբեր">
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
            <Table titles={titles} data={categories} goToEdit={(id) => navigator(`/groups/${id}`)}
                   deleteItem={deleteItem}/>
        </ComponentCard>
    );
}
