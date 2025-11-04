import './bootstrap.js'
import React, {StrictMode} from 'react';
import ReactDOM from 'react-dom/client';
import App from './pages/App.jsx';
import {ThemeProvider} from "@/context/ThemeContext.jsx";
import Preloader from "@/components/partials/Preloader.jsx";

const root = ReactDOM.createRoot(document.getElementById('app'));
root.render(

         <ThemeProvider>

            <App />
        </ThemeProvider>

    );
