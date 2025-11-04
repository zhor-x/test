import React from 'react';

const DeleteModal = ({ isOpen, onClose, onDelete, data }) => {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-[99999] flex items-center justify-center p-5 overflow-y-auto">
            {/* Overlay */}
            <div
                className="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
                onClick={onClose}
            ></div>

            {/* Modal Content */}
            <div className="relative w-full max-w-[507px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10 z-10">
                <div className="text-center">
                    <h4 className="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                        Հաստատե՞լ {data.title}-ի ջնջումը
                    </h4>

                    <p className="text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Դուք համոզվա՞ծ եք, որ ցանկանում եք ջնջել այս տարրը։ Այս գործողությունը չեք կարողանա հետ բերել։
                    </p>

                    <div className="flex items-center justify-center w-full gap-3 mt-8">
                        <button
                            onClick={onClose}
                            type="button"
                            className="flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                        >
                            Չեղարկել
                        </button>
                        <button
                            onClick={()=>onDelete(data.id)}
                            type="button"
                            className="flex justify-center px-4 py-3 text-sm font-medium text-white rounded-lg bg-red-600 shadow-theme-xs hover:bg-red-700"
                        >
                            Ջնջել
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default DeleteModal;
