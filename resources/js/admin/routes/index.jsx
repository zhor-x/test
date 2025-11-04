import Dashboard from '../pages/dashboard/Index.jsx';
import Login from '../pages/Login.jsx';
import Layout from "../components/Layout.jsx";
import Groups from "@/pages/dashboard/groups/index.jsx";
import GroupsAddEdit from "@/pages/dashboard/groups/AddEdit.jsx";
import Questions from "@/pages/dashboard/questions/index.jsx";
import QuestionsAddEdit from "@/pages/dashboard/questions/AddEdit.jsx";
import Tests from "@/pages/dashboard/tests/index.jsx";
import TestsAddEdit from "@/pages/dashboard/tests/AddEdit.jsx";
import Users from "@/pages/dashboard/users/index.jsx";
import UsersAddEdit from "@/pages/dashboard/users/AddEdit.jsx";

import UserGroups from "@/pages/dashboard/userGroups/index.jsx";
import UserGroupsAddEdit from "@/pages/dashboard/userGroups/AddEdit.jsx";
import Results from "@/pages/dashboard/users/Results.jsx";

import SignGroups from "@/pages/dashboard/signGroups/index.jsx";
import SignGroupsAddEdit from "@/pages/dashboard/signGroups/AddEdit.jsx";
import RoadSigns from "@/pages/dashboard/roadSigns/index.jsx";
import RoadSignAddEdit from "@/pages/dashboard/roadSigns/AddEdit.jsx";
 export const appRoutes = [
    {
        path: '/',
        element: <Layout/>,
        children: [
            {
                path: '',
                element: <Dashboard/>,
                name: 'home',
                meta: {title: 'Home', middleware: 'auth'},
            },

            {
                path: '/groups',
                element: <Groups/>,
                name: 'home',
                meta: {title: 'Category', middleware: 'auth'},
            },
            {
                path: '/groups/new',
                element: <GroupsAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Category Add', middleware: 'auth'},
            },
            {
                path: '/groups/:id',
                element: <GroupsAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Category Edit', middleware: 'auth'},
            },

            {
                path: '/questions',
                element: <Questions/>,
                name: 'home',
                meta: {title: 'Questions', middleware: 'auth'},
            },
            {
                path: '/questions/new',
                element: <QuestionsAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Questions Add', middleware: 'auth'},
            },
            {
                path: '/questions/:id',
                element: <QuestionsAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Questions Edit', middleware: 'auth'},
            },
            {
                path: '/tests',
                element: <Tests/>,
                name: 'home',
                meta: {title: 'Questions', middleware: 'auth'},
            },
            {
                path: '/tests/new',
                element: <TestsAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Questions Add', middleware: 'auth'},
            },
            {
                path: '/tests/:id',
                element: <TestsAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Questions Edit', middleware: 'auth'},
            },

            {
                path: '/users',
                element: <Users/>,
                name: 'users',
                meta: {title: 'Users', middleware: 'auth'},
            },
            {
                path: '/users/new',
                element: <UsersAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Users Add', middleware: 'auth'},
            },
            {
                path: '/users/:id',
                element: <UsersAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Users Edit', middleware: 'auth'},
            },

            {
                path: '/user-groups',
                element: <UserGroups/>,
                name: 'users',
                meta: {title: 'Users', middleware: 'auth'},
            },
            {
                path: '/user-groups/new',
                element: <UserGroupsAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Users Add', middleware: 'auth'},
            },
            {
                path: '/user-groups/:id',
                element: <UserGroupsAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Users Edit', middleware: 'auth'},
            },
            {
                path: '/users/:id/results',
                element: <Results/>,
                name: 'profile',
                meta: {title: 'Users profile', middleware: 'auth'},
            },


            {
                path: '/road-sign-groups',
                element: <SignGroups/>,
                name: 'users',
                meta: {title: 'Road Sign Groups', middleware: 'auth'},
            },
            {
                path: '/road-sign-groups/new',
                element: <SignGroupsAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Road Sign Groups Add', middleware: 'auth'},
            },
            {
                path: '/road-sign-groups/:id',
                element: <SignGroupsAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Road Sign Groups Edit', middleware: 'auth'},
            },

            {
                path: '/road-signs',
                element: <RoadSigns/>,
                name: 'road ',
                meta: {title: 'Road Sign', middleware: 'auth'},
            },
            {
                path: '/road-signs/new',
                element: <RoadSignAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Road Sign Add', middleware: 'auth'},
            },
            {
                path: '/road-signs/:id',
                element: <RoadSignAddEdit/>,
                name: 'add-edit',
                meta: {title: 'Road Sign Edit', middleware: 'auth'},
            },

        ],
    },
    {
        path: '/login',
        element: <Login/>,
        name: 'login',
        meta: {
            title: 'Login',
            middleware: null,
        },
    },
];
