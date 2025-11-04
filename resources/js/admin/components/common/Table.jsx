import React, {useState} from 'react';

export default function Table({ meta, titles, data, goToEdit, deleteItem, onPageChange, results }) {
    const [sortOrder, setSortOrder] = useState('desc'); // or null

    const isImageUrl = (url) =>
        typeof url === 'string' && url.match(/\.(jpeg|jpg|gif|png|webp|svg)$/i);

    const goToPage = (page) => {
        if (page !== meta.current_page && page >= 1 && page <= meta.last_page) {
            onPageChange(page,  'id', sortOrder);
        }
    };

    const handleSortById = () => {
        const newOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        setSortOrder(newOrder);
        onPageChange(meta.current_page, 'id', newOrder);
    };

    return (
        <div className="overflow-x-auto shadow-md sm:rounded-lg">
            <table className="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    {titles.map((item, index) => (
                        <th
                            key={index}
                            className="px-6 py-3 cursor-pointer"
                            onClick={item.title === 'ID' ? handleSortById : undefined}
                        >
                            {item.title}
                            {item.title === 'ID' && (
                                <span className="ml-1">{sortOrder === 'asc' ? '🔼' : '🔽'}</span>
                            )}
                        </th>

                    ))}
                    <th className="px-6 py-3 text-right">Գործողություններ</th>
                </tr>
                </thead>
                <tbody>
                {data.map((item, rowIndex) => (
                    <tr key={rowIndex} className="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        {Object.keys(item).map((key, colIndex) => (
                            <td key={colIndex} className="px-6 py-4">
                                {isImageUrl(item[key]) ? (
                                    <img
                                        src={item[key]}
                                        alt="Նկար"
                                        className="w-16 h-16 object-contain border rounded"
                                    />
                                ) : (
                                    item[key]
                                )}
                            </td>
                        ))}

                        <td className="px-6 py-4 text-right whitespace-nowrap">
                            {results && (
                                <button
                                    onClick={() => results(item?.id)}
                                    className="mr-4 text-blue-500 hover:underline"
                                >
                                    Արդյունքներ
                                </button>
                            )}
                            <button
                                onClick={() => goToEdit(item?.id)}
                                className="mr-4 text-yellow-500 hover:underline"
                            >
                                Փոփոխել
                            </button>
                            <button
                                onClick={() => deleteItem(item)}
                                className="text-red-500 hover:underline"
                            >
                                Ջնջել
                            </button>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>

            {/* Pagination Controls */}
            {meta && (
                <div className="flex items-center justify-between p-4">
                    <button
                        onClick={() => goToPage(meta.current_page - 1)}
                        disabled={meta.current_page <= 1}
                        className="px-3 py-1 bg-gray-200 rounded-lg disabled:opacity-50"
                    >
                        Նախորդ
                    </button>
                    <span>
            Էջ {meta.current_page} / {meta.last_page}
          </span>
                    <button
                        onClick={() => goToPage(meta.current_page + 1)}
                        disabled={meta.current_page >= meta.last_page}
                        className="px-3 py-1 bg-gray-200 rounded-lg disabled:opacity-50"
                    >
                        Հաջորդ
                    </button>
                </div>
            )}
        </div>
    );
}
