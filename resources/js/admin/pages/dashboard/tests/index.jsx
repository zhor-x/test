import ComponentCard from "@/components/common/ComponentCard.jsx";
import Table from "@/components/common/Table.jsx";
import {useEffect, useState} from "react";
import {useNavigate} from "react-router-dom";
import {useLocation} from "react-router";
import {SuccessNotification} from "@/components/common/SuccessNotification.jsx";
import QuestionAPI from "@/services/api/QuestionAPI.js";
import DeleteModal from "@/components/modals/DeleteModal.jsx";
import TestAPI from "@/services/api/TestAPI.js";

export default function Tests() {
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
        {title: 'Անուն'},
        {title: 'Ժամանակ'},
        {title: 'Սխալների քանակ'},
        {title: 'Ցուցադրված է'},
        {title: 'Հարցերի քանակ'},
    ];

    const getQuestions = async (page = 1, type='id', order='desc') => {
        try {
            const resp = await TestAPI.getList(page, `&type=${type}&order=${order}`); // <-- pass page number
            setMeta(resp.meta);
            setQuestions(resp.data.map((item) => ({
                id: item.id,
                title: item.title,
                duration: item.duration + ' րոպե',
                max_wrong_answers: item.max_wrong_answers + ' սխալ',
                hidden: item.hidden ? 'Այո' : 'Ոչ',
                questions_count: item.questions_count ?? 0 + ' հարց',
            })));

            console.log(resp);
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
        await TestAPI.deleteTest(id);
        setSuccessMessage('Հարցը հաջողությամբ ջնջված է');
        setQuestions((prevQuestions) =>
            prevQuestions.filter((question) => question.id !== id)
        );
        setShowSuccess(true)
    }

    return (
        <ComponentCard newButton={{title: 'Նոր թեստ', link: '/tests/new'}} title="Տեսական քննության թեստեր">
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
                goToEdit={(id) => navigator(`/tests/${id}`)}
                deleteItem={deleteItem}
                onPageChange={(page, type, order) => getQuestions(page, type, order)}
            />
        </ComponentCard>
    );
}
