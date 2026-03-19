// route-tenants.ts
import type { RouteRecordRaw } from "vue-router";
import TenantLoginPage from "../pages/tenant/TenantLoginPage.vue";
import TenantDashboardPage from "../pages/tenant/TenantDashboardPage.vue";

const AppLayout = () => import("../layouts/AppLayout.vue");

const ReportsPage = () => import("../pages/tenant/ReportsPage.vue");
const AppointmentsPage = () => import("../pages/tenant/AppointmentsPage.vue");
const SubscriptionsPage = () => import("../pages/tenant/SubscriptionsPage.vue");
const InquiriesPage = () => import("../pages/tenant/InquiriesPage.vue");
const QuotationsPage = () => import("../pages/tenant/QuotationsPage.vue");
const ServicesPage = () => import("../pages/tenant/ServicesPage.vue");
const OffersPage = () => import("../pages/tenant/OffersPage.vue");
const CustomersPage = () => import("../pages/tenant/CustomersPage.vue");
const StaffsPage = () => import("../pages/tenant/StaffsPage.vue");
const QuotationTemplatesPage = () => import("../pages/tenant/QuotationTemplatesPage.vue");
const EmailTemplatesPage = () => import("../pages/tenant/EmailTemplatesPage.vue");
const EmailTemplateDetailPage = () => import("../pages/tenant/EmailTemplateDetailPage.vue");
const DefaultSettingsPage = () => import("../pages/tenant/DefaultSettingsPage.vue");
const WorkingHoursPage = () => import("../pages/tenant/WorkingHoursPage.vue");
const PaymentCredentialsPage = () => import("../pages/tenant/PaymentCredentialsPage.vue");
const DocumentsPage = () => import("../pages/tenant/DocumentsPage.vue");

export const tenantMenuGroups = [
    {
        group: "Overview",
        items: [
            { title: "Dashboard", routeName: "tenant.dashboard", icon: "mdi-view-dashboard-outline" },
            { title: "Reports", routeName: "tenant.reports", icon: "mdi-file-chart-outline" },
        ],
    },
    {
        group: "Sales",
        items: [
            { title: "Appointments", routeName: "tenant.appointments", icon: "mdi-calendar-check" },
            { title: "Subscriptions", routeName: "tenant.subscriptions", icon: "mdi-card-account-details-outline" },
            { title: "Inquiries", routeName: "tenant.inquiries", icon: "mdi-message-question-outline" },
            { title: "Quotations", routeName: "tenant.quotations", icon: "mdi-file-document-outline" },
        ],
    },
    {
        group: "Operations",
        items: [
            { title: "Services", routeName: "tenant.services", icon: "mdi-briefcase-outline" },
            { title: "Offers", routeName: "tenant.offers", icon: "mdi-tag-outline" },
        ],
    },
    {
        group: "Accounts",
        items: [
            { title: "Customers", routeName: "tenant.customers", icon: "mdi-account-group-outline" },
            { title: "Staffs", routeName: "tenant.staffs", icon: "mdi-account-tie-outline" },
        ],
    },
    {
        group: "Templates",
        items: [
            { title: "Quotation Templates", routeName: "tenant.quotation-templates", icon: "mdi-file-document-outline" },
            { title: "Email Templates", routeName: "tenant.email-templates", icon: "mdi-email-outline" },
        ],
    },
    {
        group: "Settings",
        items: [
            { title: "Default Settings", routeName: "tenant.default-settings", icon: "mdi-cog-outline" },
            // { title: "Working Hours", routeName: "tenant.working-hours", icon: "mdi-clock-outline" },
            { title: "Payment Credentials", routeName: "tenant.payment-credentials", icon: "mdi-credit-card-outline" },
            // { title: "Documents", routeName: "tenant.documents", icon: "mdi-file-cabinet" },
        ],
    },
];

export const tenantRoutes: RouteRecordRaw[] = [
    {
        path: "login",
        name: "tenant.login",
        component: TenantLoginPage,
        meta: { title: "Login" }
    },
    {
        path: "",
        redirect: "dashboard",
    },
    {
        path: "",
        component: AppLayout,
        meta: { context: "tenant", menu: tenantMenuGroups },
        children: [
            {
                path: "dashboard",
                name: "tenant.dashboard",
                component: TenantDashboardPage,
                meta: { breadcrumb: ["Overview", "Dashboard"] }
            },
            {
                path: "reports",
                name: "tenant.reports",
                component: ReportsPage,
                meta: { breadcrumb: ["Overview", "Reports"] }
            },
            // Sales
            {
                path: "appointments",
                name: "tenant.appointments",
                component: AppointmentsPage,
                meta: { breadcrumb: ["Sales", "Appointments"] }
            },
            {
                path: "subscriptions",
                name: "tenant.subscriptions",
                component: SubscriptionsPage,
                meta: { breadcrumb: ["Sales", "Subscriptions"] }
            },
            {
                path: "inquiries",
                name: "tenant.inquiries",
                component: InquiriesPage,
                meta: { breadcrumb: ["Sales", "Inquiries"] }
            },
            {
                path: "quotations",
                name: "tenant.quotations",
                component: QuotationsPage,
                meta: { breadcrumb: ["Sales", "Quotations"] }
            },
            // Operations
            {
                path: "services",
                name: "tenant.services",
                component: ServicesPage,
                meta: { breadcrumb: ["Operations", "Services"] }
            },
            {
                path: "services/:id",
                name: "tenant.service-detail",
                component: () => import("../pages/tenant/ServiceDetailPage.vue"),
                meta: { breadcrumb: ["Operations", "Services", "Detail"] }
            },
            {
                path: "offers",
                name: "tenant.offers",
                component: OffersPage,
                meta: { breadcrumb: ["Operations", "Offers"] }
            },
            // Accounts
            {
                path: "customers",
                name: "tenant.customers",
                component: CustomersPage,
                meta: { breadcrumb: ["Accounts", "Customers"] }
            },
            {
                path: "customers/:id",
                name: "tenant.customer-detail",
                component: () => import("../pages/tenant/CustomerDetailPage.vue"),
                meta: { breadcrumb: ["Accounts", "Customers", "Detail"] }
            },
            {
                path: "staffs",
                name: "tenant.staffs",
                component: StaffsPage,
                meta: { breadcrumb: ["Accounts", "Staffs"] }
            },
            {
                path: "staffs/:id",
                name: "tenant.staff-detail",
                component: () => import("../pages/tenant/StaffDetailPage.vue"),
                meta: { breadcrumb: ["Accounts", "Staffs", "Detail"] }
            },
            // Templates
            {
                path: "quotation-templates",
                name: "tenant.quotation-templates",
                component: QuotationTemplatesPage,
                meta: { breadcrumb: ["Templates", "Quotation Templates"] }
            },
            {
                path: "email-templates",
                name: "tenant.email-templates",
                component: EmailTemplatesPage,
                meta: { breadcrumb: ["Templates", "Email Templates"] }
            },
            {
                path: "email-templates/:id",
                name: "tenant.email-template-detail",
                component: EmailTemplateDetailPage,
                meta: { breadcrumb: ["Templates", "Email Templates", "Detail"] }
            },
            // Settings
            {
                path: "default-settings",
                name: "tenant.default-settings",
                component: DefaultSettingsPage,
                meta: { breadcrumb: ["Settings", "Default Settings"] }
            },
            {
                path: "working-hours",
                name: "tenant.working-hours",
                component: WorkingHoursPage,
                meta: { breadcrumb: ["Settings", "Working Hours"] }
            },
            {
                path: "payment-credentials",
                name: "tenant.payment-credentials",
                component: PaymentCredentialsPage,
                meta: { breadcrumb: ["Settings", "Payment Credentials"] }
            },
            {
                path: "documents",
                name: "tenant.documents",
                component: DocumentsPage,
                meta: { breadcrumb: ["Settings", "Documents"] }
            },
        ],
    },
];
