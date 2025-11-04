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

const questionApi = {
    getList: (page, {search = "", limit = 10} = {}) =>
        safeRequest(
            api.get("/questions", {
                params: {
                    page,
                    q: search || undefined, // Avoid sending empty string if search is ""
                    limit: limit || undefined, // Avoid sending default limit if not specified
                },
            })
        ),

    createQuestion: (data) =>
        safeRequest(api.post(`/questions`, data)),

    updateQuestion: (id, data) =>
        safeRequest(api.post(`/questions/${id}`, data)),

    getQuestion: (id) =>
        safeRequest(api.get(`/questions/${id}`)),

    deleteQuestion: (id) =>
        safeRequest(api.delete(`/questions/${id}`)),
};

export default questionApi;
