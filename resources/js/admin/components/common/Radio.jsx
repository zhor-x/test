const Radio = ({
                   id,
                   name,
                   value,
                   checked,
                   label,
                   onChange,
                   className = "",
                   disabled = false,
                   error = false, // New prop for error
                   errorHint = "", // New prop for error hint
               }) => {
    return (
        <div className="flex flex-col">
            <label
                htmlFor={id}
                className={`relative flex cursor-pointer select-none items-center gap-3 text-sm font-medium ${
                    disabled
                        ? "text-gray-300 dark:text-gray-600 cursor-not-allowed"
                        : "text-gray-700 dark:text-gray-400"
                } ${className}`}
            >
                <input
                    id={id}
                    name={name}
                    type="radio"
                    value={value}
                    checked={checked}
                    onChange={() => !disabled && onChange(value)} // Prevent onChange when disabled
                    className="sr-only"
                    disabled={disabled} // Disable input
                />
                <span
                    className={`flex h-5 w-5 items-center justify-center rounded-full border-[1.25px] ${
                        checked
                            ? "border-brand-500 bg-brand-500"
                            : "bg-transparent border-gray-300 dark:border-gray-700"
                    } ${
                        disabled
                            ? "bg-gray-100 dark:bg-gray-700 border-gray-200 dark:border-gray-700"
                            : ""
                    }`}
                >
          <span
              className={`h-2 w-2 rounded-full bg-white ${
                  checked ? "block" : "hidden"
              }`}
          ></span>
        </span>
                {label}
            </label>
            {error && errorHint && (
                <span className="text-xs text-red-600 mt-1">{errorHint}</span>
            )}
        </div>
    );
};

export default Radio;
