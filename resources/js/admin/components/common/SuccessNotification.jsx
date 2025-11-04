import React, { useEffect, useState } from 'react';

export const SuccessNotification = ({ message, onClose }) => {
    const [visible, setVisible] = useState(true);
    const [progress, setProgress] = useState(100); // Start at 100%

    useEffect(() => {
        const duration = 5000; // 5 seconds
        const interval = 50; // update every 50ms
        const decrement = (interval / duration) * 100;

        const timer = setInterval(() => {
            setProgress((prev) => {
                if (prev <= 0) {
                    clearInterval(timer);
                    handleClose();
                    return 0;
                }
                return prev - decrement;
            });
        }, interval);

        return () => clearInterval(timer);
    }, []);

    const handleClose = () => {
        setVisible(false);
        setTimeout(onClose, 300);
    };

    return (
        <div
            className={`fixed top-5 right-5 z-[999999] w-full max-w-[340px] rounded-md bg-white shadow-theme-sm dark:bg-[#1E2634] transition-opacity duration-300 ${
                visible ? 'opacity-100' : 'opacity-0'
            }`}
        >
            <div className="flex items-center justify-between gap-3 p-3">
                <div className="flex items-center gap-4">
                    <div className="flex h-10 w-10 items-center justify-center rounded-lg text-green-600 dark:text-green-500 bg-green-50 dark:bg-green-500/[0.15]">
                        <svg className="fill-current" width="24" height="24" viewBox="0 0 24 24">
                            <path
                                fillRule="evenodd"
                                clipRule="evenodd"
                                d="M3.55078 12C3.55078 7.33417 7.3332 3.55176 11.999 3.55176C16.6649 3.55176 20.4473 7.33417 20.4473 12C20.4473 16.6659 16.6649 20.4483 11.999 20.4483C7.3332 20.4483 3.55078 16.6659 3.55078 12ZM15.5126 10.6333C15.8055 10.3405 15.8055 9.86558 15.5126 9.57269C15.2197 9.27979 14.7448 9.27979 14.4519 9.57269L11.1883 12.8364L9.54616 11.1942C9.25327 10.9014 8.7784 10.9014 8.4855 11.1942C8.19261 11.4871 8.19261 11.962 8.4855 12.2549L10.6579 14.4273C10.9894 14.647 11.3872 14.647 11.7186 14.4273L15.5126 10.6333Z"
                            />
                        </svg>
                    </div>
                    <h4 className="sm:text-base text-sm text-gray-800 dark:text-white/90">{message}</h4>
                </div>
                <button
                    onClick={handleClose}
                    className="text-gray-400 hover:text-gray-800 dark:hover:text-white/90 transition-colors"
                >
                    <svg className="fill-current" width="24" height="24" viewBox="0 0 24 24">
                        <path
                            fillRule="evenodd"
                            clipRule="evenodd"
                            d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
                        />
                    </svg>
                </button>
            </div>
            {/* Reverse progress bar */}
            <div className="h-1 bg-green-200 relative overflow-hidden rounded-b-md">
                <div
                    className="absolute left-0 top-0 h-full bg-green-500 transition-all"
                    style={{ width: `${progress}%` }}
                />
            </div>
        </div>
    );
};
