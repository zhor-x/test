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

const UserApi = {
    getList: (page, {search = "", limit = 10} = {}) =>
        safeRequest(
            api.get("/users", {
                params: {
                    page,
                    q: search || undefined, // Avoid sending empty string if search is ""
                    limit: limit || undefined, // Avoid sending default limit if not specified
                },
            })
        ),
    create: (data) =>
        safeRequest(api.post(`/users`, data)),

    update: (id, data) =>
        safeRequest(api.put(`/users/${id}`, data)),

    getUser: (id) =>
        safeRequest(api.get(`/users/${id}`)),

    getResult: (id) =>
        safeRequest(api.get(`/users/${id}/results`)),

    deleteUser: (id) =>
        safeRequest(api.delete(`/users/${id}`)),
};

export default UserApi;
