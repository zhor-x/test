import { SidebarProvider, useSidebar } from "../context/SidebarContext";
import { Outlet } from "react-router";
import Header from "./partials/Header";
import Backdrop from "./partials/Backdrop";
import Sidebar from "./partials/SideBar";
import {ThemeProvider} from "@/context/ThemeContext.jsx";
import Preloader from "@/components/partials/Preloader.jsx";
import React from "react";

const LayoutContent = () => {
    const { isExpanded, isHovered, isMobileOpen } = useSidebar();

    return (
        <ThemeProvider>
        <div className="min-h-screen xl:flex">
            <div>
                <Sidebar />
                <Backdrop />
            </div>
            <div
                className={`flex-1 transition-all duration-300 ease-in-out ${
                    isExpanded || isHovered ? "lg:ml-[290px]" : "lg:ml-[90px]"
                } ${isMobileOpen ? "ml-0" : ""}`}
            >
                <Header />
                <main>
                <div className="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                    <Outlet />
                </div>
                </main>
            </div>
        </div>
        </ThemeProvider>
    );
};

const Layout = () => {
    return (
        <SidebarProvider>
            <Preloader/>
            <LayoutContent />
        </SidebarProvider>
    );
};

export default Layout;
