import {createContext, useCallback, useContext, useEffect, useState} from 'react';
import api from "@/services/api.js";

const CategoryContext = createContext();

export const useCategory = () => useContext(CategoryContext);

export const CategoryProvider = ({children}) => {
    const [category, setCategory] = useState(null);

    const fetchCategory = useCallback(async () => {
        const token = localStorage.getItem('token');
        if (!token) {
            setLoading(false);
            return;
        }

        try {
            const response = await api.get('/category');
            serCategory(response.data);
        } catch (error) {
            console.error('Failed to fetch user:', error);
            localStorage.removeItem('token');
            setCategory(null);
        }
        
    }, []);

    useEffect(() => {
        fetchUser();
    }, [fetchUser]);
    return (
        <CategoryContext.Provider value={{category, setCategory}}>
            {children}
        </CategoryContext.Provider>
    );
};
