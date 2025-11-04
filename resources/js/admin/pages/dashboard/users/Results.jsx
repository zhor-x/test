import ComponentCard from "@/components/common/ComponentCard.jsx";
import {useEffect} from "react";
import UserApi from "@/services/api/UserAPI.js";
import {useParams} from "react-router-dom";


export default function Results() {
    const {id} = useParams();

    const getUserResult = async () => {
        try {
            const resp = await UserApi.getResult(id);
            console.log(resp)
        }catch (error) {

        }finally {

        }
    }

    useEffect(() => {

        getUserResult()
    }, [id]);
    return (
        <ComponentCard  title="Օգտատերեր">

        </ComponentCard>
    );
}
