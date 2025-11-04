import PageBreadcrumb from "@/components/common/PageBreadcrumb.jsx";

export default function Dashboard() {
    return (
        <>
            <PageBreadcrumb/>

            <div className="space-y-6">
                <div className="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div className="px-5 py-4 sm:px-6 sm:py-5">
                        <h3 className="text-base font-medium text-gray-800 dark:text-white/90">
                            Basic Form
                        </h3>
                    </div>
                </div>
            </div>

        </>
    );
}
