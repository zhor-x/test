import {useEffect, useState} from "react";

const Select = ({
                    options,
                    placeholder = "Select an option",
                    onChange,
                    className = "",
                    defaultValue = "",
                    error = false,  // Error flag
                    hint = "",
                 }) => {
    const [selectedValue, setSelectedValue] = useState(defaultValue);
    const handleChange = (e) => {
        const value = e.target.value;
        setSelectedValue(value);
        onChange(value);
    };

    // Class handling based on error state
    let selectClasses = `h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 ${className}`;

    if (error) {
        selectClasses += ` border-error-500 focus:border-error-300 focus:ring-error-500/20 dark:text-error-400 dark:border-error-500 dark:focus:border-error-800`;
    } else {
        selectClasses += selectedValue ? " text-gray-800 dark:text-white/90" : " text-gray-400 dark:text-gray-400";
    }

    useEffect(() => {
        setSelectedValue(defaultValue)
    }, [defaultValue]);
    return (
        <div className="relative">
            <select
                className={selectClasses}
                value={selectedValue}
                onChange={handleChange}
            >
                <option
                    value=""
                    disabled
                    className="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                >
                    {placeholder}
                </option>
                {options.map((option) => (
                    <option
                        key={option.id}
                        value={option.id}
                        className="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                    >
                        {option.title}
                    </option>
                ))}
            </select>

            {hint && (
                <p
                    className={`mt-1.5 text-xs ${
                        error ? "text-error-500" : "text-gray-500"
                    }`}
                >
                    {hint}
                </p>
            )}
        </div>
    );
};

export default Select;
