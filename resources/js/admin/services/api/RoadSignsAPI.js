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

const RoadSignsAPI = {
    getList: (page, {search = "", limit = 10} = {}) =>
        safeRequest(
            api.get("/road-signs", {
                params: {
                    page,
                    q: search || undefined, // Avoid sending empty string if search is ""
                    limit: limit || undefined, // Avoid sending default limit if not specified
                },
            })
        ),

    create: (data) =>
        safeRequest(api.post(`/road-signs`, data)),

    update: (id, data) =>
        safeRequest(api.post(`/road-signs/${id}`, data)),

    getById: (id) =>
        safeRequest(api.get(`/road-signs/${id}`)),

    destroy: (id) =>
        safeRequest(api.delete(`/road-signs/${id}`)),
};

export default RoadSignsAPI;
