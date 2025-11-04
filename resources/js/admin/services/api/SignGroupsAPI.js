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

const SignGroupsAPI = {
    getList: () =>
        safeRequest(api.get(`/sign-groups`)),

    create: (data) =>
        safeRequest(api.post(`/sign-groups`, data)),

    update: (data) =>
        safeRequest(api.put(`/sign-groups/${data.id}`, data)),

    getById: (id) =>
        safeRequest(api.get(`/sign-groups/${id}`)),

    destroy: (id) =>
        safeRequest(api.delete(`/sign-groups/${id}`)),
};

export default SignGroupsAPI;
