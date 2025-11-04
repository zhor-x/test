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

const TestApi = {
    getList: (page, type) =>
        safeRequest(api.get(`/tests?page=${page}${type}&limit=20`)),

    createTest: (data) =>
        safeRequest(api.post(`/tests`, data)),

    updateTest: (id, data) =>
        safeRequest(api.put(`/tests/${id}`, data)),

    getTest: (id) =>
        safeRequest(api.get(`/tests/${id}`)),

    deleteTest: (id) =>
        safeRequest(api.delete(`/tests/${id}`)),
};

export default TestApi;
