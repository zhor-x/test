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

const UserGroupApi = {
    getList: (page, {search = "", limit = 10} = {}) =>
        safeRequest(
            api.get("/user-groups", {
                params: {
                    page,
                    q: search || undefined, // Avoid sending empty string if search is ""
                    limit: limit || undefined, // Avoid sending default limit if not specified
                },
            })
        ),
    create: (data) =>
        safeRequest(api.post(`/user-groups`, data)),

    update: (id, data) =>
        safeRequest(api.put(`/user-groups/${id}`, data)),

    getUser: (id) =>
        safeRequest(api.get(`/user-groups/${id}`)),

    getUsers: (page, {search = "", limit = 10} = {}) =>
        safeRequest(
            api.get("/user-groups/users", {
                params: {
                    page,
                    q: search || undefined, // Avoid sending empty string if search is ""
                    limit: limit || undefined, // Avoid sending default limit if not specified
                },
            })
        ),
    destroy: (id) =>
        safeRequest(api.delete(`/user-groups/${id}`)),
};

export default UserGroupApi;
