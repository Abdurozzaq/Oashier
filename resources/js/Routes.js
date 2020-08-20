import multiguard from 'vue-router-multiguard';
import axios from 'axios'

import CashierLayout from "./layouts/Dashboard-Cashier.vue";
import AdminLayout from "./layouts/Dashboard-Admin.vue";
import LandingLayout from "./layouts/Landing.vue"

// For Auth
import Login from "./pages/auth/Login.vue"
import Register from "./pages/auth/Register.vue"
import ForgotPassword from "./pages/auth/ForgotPassword.vue"
import ResetPassword from "./pages/auth/ResetPassword.vue"
import ResendVerificationMail from "./pages/auth/ResendVerificationMail.vue"
import RedirectAfterVerify from "./pages/auth/RedirectAfterVerify.vue"
import UnverifiedEmail from "./pages/auth/UnverifiedEmail.vue"

// FOr Cashier
import LandingPage from "./pages/LandingPage.vue"
import CashierHomePage from "./pages/cashier/HomePage.vue"
import CreateMenuPage from "./pages/cashier/menu/CreateMenu.vue"
import MenuListPage from "./pages/cashier/menu/MenuList.vue"
import EditMenuPage from "./pages/cashier/menu/EditMenu.vue"
import CreateOrderPage from "./pages/cashier/order/CreateOrder.vue"
import EditOrderMenuPage from "./pages/cashier/order/EditMenu.vue"
import OrderListPage from "./pages/cashier/order/OrderList.vue"
import CancelledOrderListPage from "./pages/cashier/order/CancelledOrderList.vue"
import SuccessedOrderListPage from "./pages/cashier/order/SuccessedOrderList.vue"
import IdentitySettingsPage from "./pages/cashier/settings/IdentitySettings.vue"
import PasswordSettingPage from "./pages/cashier/settings/PasswordSetting.vue"

// For Admin
import AdminIdentitySettingsPage from "./pages/admin/settings/IdentitySettings.vue"
import AdminPasswordSettingPage from "./pages/admin/settings/PasswordSetting.vue"
import AdminCreateUserPage from "./pages/admin/users/CreateUser.vue"
import AdminCashierUsersListPage from "./pages/admin/users/CashierUsersList.vue"
import AdminAdminUsersListPage from "./pages/admin/users/AdminUsersList.vue"

import Component from "./components/ExampleComponent.vue"

const token = localStorage.getItem('userToken')
if (token) {
  axios.defaults.headers.common['Authorization'] = 'Bearer' + ' ' + token
}

/**
 *
 * For Authenticated
 * And Not Authenticated
 *
 * Guard
 */
const ifAuthenticated = (to, from, next) => {
    if (localStorage.getItem('userToken')) {
        next()
        return
    } else {
        next('/login')
    }
}

const ifNotAuthenticated = (to, from, next) => {

    if (localStorage.hasOwnProperty('userToken') === false) {
        next()
    } else {

            axios.get('api/auth/me', {
                headers: {
                  Authorization: 'Bearer ' + token,
                  withCredentials: true //the token is a variable which holds the token
                }
               })
                .then(function (response) {
                    // handle success
                    let userRole = response.data.role
                    if (userRole == "admin") {
                        next('/siAdmino')
                        return
                    } else {
                        next('/home')
                    }
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
    }
}


/**
 * Guard For
 * Admin Only
 * &
 * User Only
 */
const adminOnly = (to, from, next) => {
        axios.get('api/auth/me')
            .then(function (response) {
                // handle success
                let userRole = response.data.role
                if (userRole == "admin") {
                    next()
                    return
                } else {
                    next('/login')
                    return
                }
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            })
}

const userOnly = (to, from, next) => {

        axios.get('api/auth/me')
            .then(function (response) {
                // handle success
                let userRole = response.data.role
                if (userRole == "cashier") {
                    next()
                    return
                } else {
                    next('/login')
                    return
                }
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            })
}

/**
 * Guard For
 * Verified User Email
 */
const verifiedEmail = (to, from, next) => {

        axios.get('api/auth/me')
            .then(function (response) {
                // handle success
                let isVerified = response.data.user.email_verified_at
                if (isVerified) {
                    next()
                    return
                } else {
                    next('/UnverifiedEmail')
                    return
                }
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            })
}

/**
 * Guard For
 * Un Verified User Email
 */
const unVerifiedEmail = (to, from, next) => {

    axios.get('api/auth/me')
        .then(function (response) {
            // handle success
            let isVerified = response.data.user.email_verified_at
            if (isVerified == null) {
                next('/UnverifiedEmail')
                return
            } else {
                next()
                return
            }
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        })
}

const pageTitle = (to, from, next) => {
    document.title = to.meta.title
    next()
}


export const routes = [
    {
        path: "",
        component: LandingLayout,
        children: [
            {
                path: "",
                meta: {
                    title: 'Welcome - OASHIER',
                },
                component: LandingPage,
            }
        ]
    },
    {
        path: "/home",
        component: CashierLayout,
        children: [
            {
                path: "",
                component: CashierHomePage,
                meta: {
                    title: 'Home - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, userOnly, verifiedEmail]),
            },
            {
                path: "menu/create",
                component: CreateMenuPage,
                meta: {
                    title: 'Create Menu - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, userOnly, verifiedEmail]),
            },
            {
                path: "menu/list",
                component: MenuListPage,
                meta: {
                    title: 'Menu List - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, userOnly, verifiedEmail]),
            },
            {
                path: "menu/edit",
                component: EditMenuPage,
                meta: {
                    title: 'Edit Menu - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, userOnly, verifiedEmail]),
            },
            {
                path: "order/create",
                component: CreateOrderPage,
                meta: {
                    title: 'Create Order - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, userOnly, verifiedEmail]),
            },
            {
                path: "order/edit",
                component: EditOrderMenuPage,
                meta: {
                    title: 'Edit Order Menu - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, userOnly, verifiedEmail]),
            },
            {
                path: "order/list",
                component: OrderListPage,
                meta: {
                    title: 'Order List - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, userOnly, verifiedEmail]),
            },
            {
                path: "order/cancelled/list",
                component: CancelledOrderListPage,
                meta: {
                    title: 'Cancelled Order List - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, userOnly, verifiedEmail]),
            },
            {
                path: "order/successed/list",
                component: SuccessedOrderListPage,
                meta: {
                    title: 'Successed Order List - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, userOnly, verifiedEmail]),
            },
            {
                path: "settings/identity",
                component: IdentitySettingsPage,
                meta: {
                    title: 'Identity Setting - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, userOnly, verifiedEmail]),
            },
            {
                path: "settings/password",
                component: PasswordSettingPage,
                meta: {
                    title: 'Change Password - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, userOnly, verifiedEmail]),
            }
        ]
    },
    {
        path: "/siAdmino",
        component: AdminLayout,
        children: [
            {
                path: "",
                component: Component,
                beforeEnter: multiguard([ifAuthenticated, adminOnly, verifiedEmail]),
            },
            {
                path: "settings/identity",
                component: AdminIdentitySettingsPage,
                meta: {
                    title: 'Identity Setting - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, adminOnly, verifiedEmail]),
            },
            {
                path: "settings/password",
                component: AdminPasswordSettingPage,
                meta: {
                    title: 'Change Password - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, adminOnly, verifiedEmail]),
            },
            {
                path: "users/create",
                component: AdminCreateUserPage,
                meta: {
                    title: 'Create User - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, adminOnly, verifiedEmail]),
            },
            {
                path: "users/cashier/list",
                component: AdminCashierUsersListPage,
                meta: {
                    title: 'Cashier Users List - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, adminOnly, verifiedEmail]),
            },
            {
                path: "users/admin/list",
                component: AdminAdminUsersListPage,
                meta: {
                    title: 'Admin Users List - OASHIER',
                },
                beforeEnter: multiguard([pageTitle, ifAuthenticated, adminOnly, verifiedEmail]),
            }
        ]
    },

    /**
     * For Authentication Purposes
     */
    {
        path: "/login",
        component: Login,
        meta: {
            title: 'Login - OASHIER',
        },
        beforeEnter: multiguard([ifNotAuthenticated]),
    },
    {
        path: "/register",
        component: Register,
        meta: {
            title: 'Register - OASHIER',
        },
        beforeEnter: multiguard([ifNotAuthenticated]),
    },
    {
        path: "/forgot-password",
        component: ForgotPassword,
        meta: {
            title: 'Forgot Password - OASHIER',
        },
        beforeEnter: multiguard([ifNotAuthenticated]),
    },
    {
        path: "/reset-password",
        component: ResetPassword,
        meta: {
            title: 'Reset Password - OASHIER',
        },
        beforeEnter: multiguard([ifNotAuthenticated]),
    },
    {
        path: "/resend-verification-mail",
        component: ResendVerificationMail,
        meta: {
            title: 'Resend Verification Mail - OASHIER',
        },
        beforeEnter: multiguard([ifNotAuthenticated]),
    },
    {
        path: "/verification-success",
        component: RedirectAfterVerify,
        meta: {
            title: 'Verification Success - OASHIER',
        },
    },
    {
        path: "/UnverifiedEmail",
        component: UnverifiedEmail,
        meta: {
            title: 'Unverified Email Address - OASHIER',
        },
    }
];
