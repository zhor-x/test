import ComponentCard from "@/components/common/ComponentCard.jsx";
import Table from "@/components/common/Table.jsx";
import {useEffect, useState} from "react";
import {useNavigate} from "react-router-dom";
import {useLocation} from "react-router";
import {SuccessNotification} from "@/components/common/SuccessNotification.jsx";
import QuestionAPI from "@/services/api/QuestionAPI.js";
import DeleteModal from "@/components/modals/DeleteModal.jsx";

export default function Questions() {
    const [isModalOpen, setModalOpen] = useState(false);
    const [deletedModalData, setDeletedModalData] = useState(null);

    const location = useLocation();
    const [showSuccess, setShowSuccess] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");
    const [questions, setQuestions] = useState([]);
    const [meta, setMeta] = useState([]);
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
        {title: 'Հարց'},
        {title: 'Նկար'},
        {title: 'Խումբ'},
        {title: 'Թեստեր'},
    ];

    const getQuestions = async (page = 1) => {
        try {
            const resp = await QuestionAPI.getList(page); // <-- pass page number
            setMeta(resp.meta);
            setQuestions(resp.data.map((item) => ({
                id: item.id,
                text: item.text,
                image: item.image,
                groups: item.group,
                exam: '',
            })));
        } catch (error) {
            console.log(error);
        }
    };

    useEffect(() => {
        getQuestions()
    }, []);

    const deleteItem = (item) => {
        setDeletedModalData({
            id: item.id,
            title: item.text
        })


        setModalOpen(true)
    }


    const handleDelete = async (id) => {
        setModalOpen(false)
        await QuestionAPI.deleteQuestion(id);
        setSuccessMessage('Հարցը հաջողությամբ ջնջված է');
        setQuestions((prevQuestions) =>
            prevQuestions.filter((question) => question.id !== id)
        );
        setShowSuccess(true)
    }

    return (
        <ComponentCard newButton={{title: 'Նոր Խումբ', link: '/questions/new'}} title="Հարցեր">
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
            <Table
                meta={meta}
                titles={titles}
                data={questions}
                goToEdit={(id) => navigator(`/questions/${id}`)}
                deleteItem={deleteItem}
                onPageChange={(page) => getQuestions(page)}
            />
        </ComponentCard>
    );
}
