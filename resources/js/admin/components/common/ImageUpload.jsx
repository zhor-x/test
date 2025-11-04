import React, { useState, useEffect } from "react";

const ImageUpload = ({ className = "", onChange, value }) => {
    const [fileName, setFileName] = useState("Նկար ընտրված չէ");
    const [preview, setPreview] = useState(null);

    // Sync preview with value prop (e.g., existing image URL)
    useEffect(() => {
        if (value) {
            if (typeof value === "string") {
                // Existing image URL
                setPreview(value);
                setFileName("Հարցի նկար"); // Or extract filename from URL if needed
            } else if (value instanceof File) {
                // New file
                setFileName(value.name);
                const reader = new FileReader();
                reader.onloadend = () => {
                    setPreview(reader.result);
                };
                reader.readAsDataURL(value);
            }
        } else {
            // No image
            setPreview(null);
            setFileName("Նկար ընտրված չէ");
        }
    }, [value]);

    const handleChange = (e) => {
        const file = e.target.files[0];

        if (file && file.type.startsWith("image/")) {
            setFileName(file.name);
            const reader = new FileReader();
            reader.onloadend = () => {
                setPreview(reader.result);
            };
            reader.readAsDataURL(file);
            onChange(file); // Pass the File object to parent
        } else {
            setFileName("Նկար ընտրված չէ");
            setPreview(null);
            onChange(null); // Clear the image in parent
        }
    };

    const handleClear = () => {
        setFileName("Նկար ընտրված չէ");
        setPreview(null);
        onChange(null); // Clear the image in parent
    };

    return (
        <div className={`w-full ${className}`}>
            <label className="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                Վերբեռնել նկար
            </label>

            <div className="flex items-center space-x-2">
                <label
                    htmlFor="imageUpload"
                    className="cursor-pointer flex items-center justify-start w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2 text-sm text-gray-700 shadow-sm hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                >
                    <span className="mr-2">Ընտրել նկար</span>
                    <span className="font-medium text-brand-600 dark:text-brand-400">
            <span className="truncate">{fileName}</span>
          </span>
                </label>

                {preview && (
                    <button
                        type="button"
                        onClick={handleClear}
                        className="text-sm text-red-600 hover:underline"
                    >
                        Հեռացնել
                    </button>
                )}
            </div>

            <input
                id="imageUpload"
                type="file"
                accept="image/*"
                className="hidden"
                onChange={handleChange}
            />

            {preview && (
                <div className="mt-4">
                    <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        Նախադիտում՝
                    </p>
                    <img
                        src={preview}
                        alt="Նկարի նախադիտում"
                        width={200}
                        height={200}
                        className="max-w-full h-auto rounded-md border border-gray-300 dark:border-gray-700"
                    />
                </div>
            )}
        </div>
    );
};

export default ImageUpload;
