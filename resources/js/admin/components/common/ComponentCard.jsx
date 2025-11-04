import {Link} from "react-router";

const ComponentCard = ({title, children, className = "", desc = "", newButton=null}) => {
    return (
        <div
            className={`rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] ${className}`}
        >
            <div className="px-6 py-5">
                <div className="flex items-center justify-between">
                    <div>
                        <h3 className="text-base font-medium text-gray-800 dark:text-white/90">
                            {title}
                        </h3>
                        {desc && (
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {desc}
                            </p>
                        )}
                    </div>

                    {newButton && (
                        <Link to={newButton.link}
                            className="p-3 text-sm font-medium text-white transition-colors rounded-lg bg-brand-500 hover:bg-brand-600">
                            {newButton.title}
                        </Link>
                    )}
                </div>
            </div>


            {/* Card Body */}
            <div className="p-4 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                <div className="space-y-6">{children}</div>
            </div>


        </div>
    );
};

export default ComponentCard;
