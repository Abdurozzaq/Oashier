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


import LandingPage from "./pages/LandingPage.vue"
import CashierHomePage from "./pages/cashier/HomePage.vue"
import CreateMenuPage from "./pages/cashier/menu/CreateMenu.vue"
import MenuListPage from "./pages/cashier/menu/MenuList.vue"
import EditMenuPage from "./pages/cashier/menu/EditMenu.vue"
import CreateOrderPage from "./pages/cashier/order/CreateOrder.vue"
import EditOrderMenuPage from "./pages/cashier/order/EditMenu.vue"
import OrderListPage from "./pages/cashier/order/OrderList.vue"
import CancelledOrderListPage from "./pages/cashier/order/CancelledOrderList.vue"

import Component from "./components/ExampleComponent.vue"

const token = localStorage.getItem('userToken')
if (token) {
  axios.defaults.headers.common['Authorization'] = 'Bearer' + ' ' + token
}

console.log('route' + " " + token)

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
        beforeEnter: multiguard([ifNotAuthenticated]),
    }
];
