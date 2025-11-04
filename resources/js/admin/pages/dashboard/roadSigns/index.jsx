import ComponentCard from "@/components/common/ComponentCard.jsx";
import Table from "@/components/common/Table.jsx";
import {useEffect, useState} from "react";
import {useNavigate} from "react-router-dom";
import {useLocation} from "react-router";
import {SuccessNotification} from "@/components/common/SuccessNotification.jsx";
import DeleteModal from "@/components/modals/DeleteModal.jsx";
import RoadSignsAPI from "@/services/api/RoadSignsAPI.js";

export default function RoadSigns() {
    const [deletedModalData, setDeletedModalData] = useState(null);
    const location = useLocation();

    const [showSuccess, setShowSuccess] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");
    const [roadSigns, setRoadSigns] = useState([]);
    const [isModalOpen, setModalOpen] = useState(false);

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
        {title: 'Նկար'},
        {title: 'Նկարագրություն'},
    ];

    const getList = async (page = 1) => {
        try {
            const resp = await RoadSignsAPI.getList(page); // <-- pass page number
            setMeta(resp.meta);
            setRoadSigns(resp.data.map((item) => ({
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
        getList()
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
        await RoadSignsAPI.destroy(id);
        setSuccessMessage('Նշանը հաջողությամբ ջնջված է');
        setRoadSigns((prevState) =>
            prevState.filter((sign) => sign.id !== id)
        );
        setShowSuccess(true)
    }

    return (
        <ComponentCard newButton={{title: 'Նոր Նշան', link: '/road-signs/new'}} title="Հարցեր">
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
                data={roadSigns}
                goToEdit={(id) => navigator(`/road-signs/${id}`)}
                deleteItem={deleteItem}
                onPageChange={(page) => getList(page)}
            />
        </ComponentCard>
    );
}
