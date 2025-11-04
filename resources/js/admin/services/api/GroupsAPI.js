import api from '../api';

async function safeRequest(promise) {
    try {
        const res = await promise;
        return {...res.data, status: res.status};
    } catch (err) {
        console.error('API error:', err);
        throw err?.response?.data || err;
    }
}

const groupsAPI = {
    getCategoryList: () =>
        safeRequest(api.get(`/groups`)),

    createCategory: (data) =>
        safeRequest(api.post(`/groups`, data)),

    updateCategory: (data) =>
        safeRequest(api.put(`/groups/${data.id}`, data)),

    getCategory: (id) =>
        safeRequest(api.get(`/groups/${id}`)),

    deleteCategory: (id) =>
        safeRequest(api.delete(`/groups/${id}`)),
};

export default groupsAPI;
