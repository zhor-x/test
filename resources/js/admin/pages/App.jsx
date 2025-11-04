import React, { useContext } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, AuthContext } from '../context/AuthContext';
import { appRoutes } from '@/routes/index.jsx';

function ProtectedRoute({ children, requiresAuth }) {
    const { user, loading } = useContext(AuthContext);

    if (loading) {
        return <div>Loading...</div>;
    }

    if (requiresAuth && !user) {
        return <Navigate to="/login" replace />;
    }

    return children;
}

function App() {
    return (
        <AuthProvider>
            <BrowserRouter>
                <Routes>
                    {appRoutes.map((route) => (
                        route.children ? (
                            <Route
                                key={route.path}
                                path={route.path}
                                element={route.element}
                            >
                                {route.children.map((child) => (
                                    <Route
                                        key={child.path}
                                        path={child.path}
                                        element={child.element}
                                    />
                                ))}
                            </Route>
                        ) : (
                            <Route
                                key={route.path}
                                path={route.path}
                                element={route.element}
                            />
                        )
                    ))}
                </Routes>
            </BrowserRouter>
        </AuthProvider>
    );
}

export default App;
