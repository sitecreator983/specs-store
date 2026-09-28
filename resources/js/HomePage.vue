<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import Button from 'primevue/button';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import TieredMenu from 'primevue/tieredmenu';

const currentPath = window.location.pathname;
const categoryPages = {
    '/categories/processors': { slug: 'processors', title: 'Όλοι οι επεξεργαστές', titleEn: 'All processors', eyebrow: 'CPU collection' },
    '/categories/graphics-cards': { slug: 'graphics-cards', title: 'Όλες οι κάρτες γραφικών', titleEn: 'All graphics cards', eyebrow: 'GPU collection' },
    '/categories/storage': { slug: 'storage', title: 'NVMe & SSD', eyebrow: 'Storage collection' },
    '/categories/cases': { slug: 'cases', title: 'Κουτιά υπολογιστών', titleEn: 'PC cases', eyebrow: 'PC cases' },
    '/categories/ram': { slug: 'ram', title: 'Μνήμες RAM', titleEn: 'RAM memory', eyebrow: 'Memory collection' },
    '/categories/motherboards': { slug: 'motherboards', title: 'Μητρικές Κάρτες', titleEn: 'Motherboards', eyebrow: 'Motherboard collection' },
    '/categories/power-supplies': { slug: 'power-supplies', title: 'Τροφοδοτικά', titleEn: 'Power supplies', eyebrow: 'Power supply collection' },
    '/categories/fans': { slug: 'fans', title: 'Ανεμιστηράκια', titleEn: 'Case fans', eyebrow: 'Case fan collection' },
    '/categories/external-drives': { slug: 'external-drives', title: 'Εξωτερικοί Δίσκοι', titleEn: 'External drives', eyebrow: 'Portable storage' },
    '/categories/ssd-hdd': { slug: 'ssd-hdd', title: 'Δίσκοι SSD & HDD', titleEn: 'SSD & HDD drives', eyebrow: 'Internal storage' },
    '/categories/usb-sticks': { slug: 'usb-sticks', title: 'USB sticks', eyebrow: 'Flash storage' },
    '/categories/memory-cards': { slug: 'memory-cards', title: 'Κάρτες Μνήμης', titleEn: 'Memory cards', eyebrow: 'Memory cards' },
    '/categories/drive-enclosures': { slug: 'drive-enclosures', title: 'Θήκες σκληρών δίσκων', titleEn: 'Drive enclosures', eyebrow: 'Drive enclosures' },
    '/categories/cd-dvd': { slug: 'cd-dvd', title: 'CD-DVD', eyebrow: 'Optical media & drives' },
    '/categories/keyboards': { slug: 'keyboards', title: 'Πληκτρολόγια', titleEn: 'Keyboards', eyebrow: 'Keyboards' },
    '/categories/headsets': { slug: 'headsets', title: 'Ακουστικά', titleEn: 'Headsets', eyebrow: 'Headsets' },
    '/categories/monitors': { slug: 'monitors', title: 'Οθόνες', titleEn: 'Monitors', eyebrow: 'Monitors' },
    '/categories/mice': { slug: 'mice', title: 'Ποντίκια', titleEn: 'Mice', eyebrow: 'Mice' },
    '/categories/speakers': { slug: 'speakers', title: 'Ηχεία', titleEn: 'Speakers', eyebrow: 'Speakers' },
    '/categories/joysticks': { slug: 'joysticks', title: 'Joysticks', eyebrow: 'Controllers & joysticks' },
};
const categoryPage = categoryPages[currentPath];
const isCategoryPage = Boolean(categoryPage);
const isOffersPage = currentPath === '/offers';
const isCartPage = currentPath === '/cart';
const isWishlistPage = currentPath === '/wishlist';
const isCheckoutPage = currentPath === '/checkout';
const isCardCheckoutPage = currentPath === '/checkout/card';
const isCheckoutSuccessPage = currentPath.startsWith('/checkout/success/');
const isOrderTrackingPage = currentPath === '/track-order';
const isAccountOrdersPage = currentPath === '/account/orders';
const isAdminProductsPage = currentPath === '/admin/products';
const isPcBuilderPage = currentPath === '/pc-builder';
const isSearchPage = currentPath === '/search';
const isHomePage = computed(() => currentPath === '/');
const heroBackgroundImage = `${window.location.origin}/images/hero/pc-builder-cpu-motherboard-unsplash.jpg`;
const homeBackgroundStyle = {
    '--home-background-image': `url("${heroBackgroundImage}")`,
};
const initialSearchQuery = new URLSearchParams(window.location.search).get('q') ?? '';
const productsEndpoint = isSearchPage
    ? `/api/search-products?q=${encodeURIComponent(initialSearchQuery)}`
    : (isOffersPage ? '/api/offers' : (categoryPage ? `/api/categories/${categoryPage.slug}/products` : '/api/categories/processors/products'));
const language = ref(localStorage.getItem('specsStoreLanguage') ?? 'EL');
const searchQuery = ref(isSearchPage ? initialSearchQuery : '');
const searchPreviewProducts = ref([]);
const isSearchPreviewOpen = ref(false);
const isLoadingSearchPreview = ref(false);
let searchPreviewTimeout = null;
const isMainMenuOpen = ref(false);
const products = ref([]);
const builderProducts = ref({});
const savedBuilds = ref([]);
const selectedSavedBuild = ref(null);
const loadedSavedBuild = ref(null);
const buildName = ref('');
const builderSaveMessage = ref('');
const isLoadingSavedBuilds = ref(false);
const isSavingBuild = ref(false);
const isDeletingSavedBuild = ref(false);
const isLoadingBuilderProducts = ref(false);
const builderSelection = ref({
    cpu: null,
    motherboard: null,
    ram: null,
    gpu: null,
    storage: null,
    case: null,
    psu: null,
    fans: null,
});
const isLoadingProducts = ref(true);
const cart = ref({ items: [], count: 0, total: 0, formatted_total: '0,00€' });
const cartStockError = ref('');
const wishlist = ref({ items: [], product_ids: [], count: 0 });
const accountOrders = ref([]);
const adminOrders = ref([]);
const adminProducts = ref([]);
const adminBrands = ref([]);
const adminCategories = ref([]);
const adminProductDrafts = ref({});
const adminProductMessages = ref({});
const savingAdminProductIds = ref([]);
const deletingAdminProductIds = ref([]);
const cancellingAdminOrderIds = ref([]);
const selectedAdminCategory = ref('all');
const selectedAdminView = ref('products');
const adminOrderNameFilter = ref('');
const adminBrandForm = ref({ name: '', logo_path: '' });
const adminCategoryForm = ref({ name: '', parent_id: null });
const adminNewProductForm = ref({
    name: '',
    sku: '',
    brand_id: null,
    category_id: null,
    image_path: '',
    price_cents: 0,
    sale_price_cents: null,
    stock: 0,
    specs: [],
});
const adminCreateErrors = ref({ brand: {}, category: {}, product: {} });
const adminCreateMessages = ref({ brand: '', category: '', product: '' });
const adminCreating = ref({ brand: false, category: false, product: false });
const deletingAdminBrandIds = ref([]);
const deletingAdminCategoryIds = ref([]);
const isLoadingAccountOrders = ref(false);
const isLoadingAdminProducts = ref(false);
const isLoadingAdminOrders = ref(false);
const authUser = ref(window.specsStoreAuthUser ?? null);
const isSigningOut = ref(false);
const isSubmittingCheckout = ref(false);
const isValidatingCartStock = ref(false);
const isSubmittingCard = ref(false);
const checkoutErrors = ref({});
const cardErrors = ref({});
const trackingCode = ref(new URLSearchParams(window.location.search).get('code') ?? '');
const trackedOrder = ref(null);
const trackingError = ref('');
const isTrackingOrder = ref(false);
const checkoutForm = ref({
    first_name: '',
    last_name: '',
    phone: '',
    email: window.specsStoreAuthUser?.email ?? '',
    address: '',
    delivery_latitude: null,
    delivery_longitude: null,
    payment_method: 'CASH',
});
const deliveryPin = ref(null);
const cardForm = ref({
    cardholder_name: '',
    card_number: '',
    expiry_month: '',
    expiry_year: '',
    cvv: '',
});
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

const languageOptions = ['EL', 'EN'];

const translations = {
    'Δωρεάν μεταφορικά για παραγγελίες άνω των 50€': 'Free shipping for orders over €50',
    'Υποστήριξη: 210 123 4567': 'Support: 210 123 4567',
    'Αναζήτηση': 'Search',
    'Αναζήτηση με SKU ή μοντέλο': 'Search by SKU or model',
    'Αποτελέσματα αναζήτησης': 'Search results',
    'Δεν βρέθηκαν προϊόντα': 'No products found',
    'Δεν βρέθηκαν προϊόντα.': 'No products found.',
    'Δοκίμασε διαφορετική φράση αναζήτησης.': 'Try a different search phrase.',
    'Αποτελέσματα από όλες τις κατηγορίες': 'Results from all categories',
    'Αναζήτηση προϊόντων...': 'Searching products...',
    'Λογαριασμός': 'Account',
    'Σύνδεση': 'Sign in',
    'Αγαπημένα': 'Wishlist',
    'Καλάθι': 'Cart',
    'Κατηγορίες Προϊόντων': 'Product categories',
    'Προσφορές': 'Offers',
    'Εξαρτήματα': 'Components',
    'Επεξεργαστές': 'Processors',
    'Κάρτες γραφικών': 'Graphics cards',
    'Αποθήκευση': 'Storage',
    'Κουτιά': 'Cases',
    'Μνήμες RAM': 'RAM memory',
    'Μητρικές Κάρτες': 'Motherboards',
    'Τροφοδοτικά': 'Power supplies',
    'Ανεμιστηράκια': 'Fans',
    'Εξωτερικοί Δίσκοι': 'External drives',
    'Δίσκοι SSD & HDD': 'SSD & HDD drives',
    'Κάρτες Μνήμης': 'Memory cards',
    'Θήκες σκληρών δίσκων': 'Drive enclosures',
    'Οθόνες & Περιφερειακά': 'Monitors & peripherals',
    'Πληκτρολόγια': 'Keyboards',
    'Ακουστικά': 'Headsets',
    'Οθόνες': 'Monitors',
    'Ποντίκια': 'Mice',
    'Ηχεία': 'Speakers',
    'Στήσε το επόμενο PC σου με καθαρά specs': 'Build your next PC with clear specs',
    'Διάλεξε parts, έλεγξε συμβατότητα και κράτα το budget σου σε τάξη από την πρώτη οθόνη.': 'Pick parts, check compatibility, and keep your budget under control from the first screen.',
    'Άνοιγμα PC Builder': 'Open PC Builder',
    'Προτεινόμενη σύνθεση': 'Suggested build',
    'Διάλεξε συμβατά εξαρτήματα από τη βάση': 'Choose compatible components from the database',
    'Καθαρισμός σύνθεσης': 'Clear build',
    'Φόρτωση αποθηκευμένης σύνθεσης': 'Load saved build',
    'Συμβατή σύνθεση': 'Compatible build',
    'Χρειάζεται αλλαγή': 'Needs changes',
    'Σύνολο': 'Total',
    'Προσθήκη σύνθεσης στο καλάθι': 'Add build to cart',
    'Όνομα σύνθεσης': 'Build name',
    'Αποθήκευση σύνθεσης': 'Save build',
    'Αποθήκευση ως νέα σύνθεση': 'Save as new build',
    'Αντικατάσταση υπάρχουσας': 'Replace existing build',
    'Συνδέσου για να αποθηκεύσεις και να φορτώσεις συνθέσεις.': 'Sign in to save and load builds.',
    'Επίλεξε επεξεργαστή': 'Choose a processor',
    'Επίλεξε μητρική': 'Choose a motherboard',
    'Επίλεξε μνήμη': 'Choose memory',
    'Επίλεξε κάρτα γραφικών': 'Choose a graphics card',
    'Επίλεξε δίσκο': 'Choose storage',
    'Επίλεξε κουτί': 'Choose a case',
    'Επίλεξε τροφοδοτικό': 'Choose a power supply',
    'Προαιρετικά ανεμιστηράκια': 'Optional case fans',
    'διαθέσιμα προϊόντα': 'available products',
    'συμβατά με τις τωρινές επιλογές': 'compatible with current choices',
    'Το καλάθι σου': 'Your cart',
    'Το καλάθι είναι άδειο': 'Your cart is empty',
    'Πρόσθεσε προϊόντα από την αρχική ή από κάποια κατηγορία.': 'Add products from the home page or a category.',
    'Συνέχεια αγορών': 'Continue shopping',
    'Ολοκλήρωση παραγγελίας': 'Checkout',
    'Τα αγαπημένα σου': 'Your wishlist',
    'Η wishlist είναι άδεια': 'Your wishlist is empty',
    'Πάτησε την καρδιά σε προϊόντα που θέλεις να κρατήσεις για αργότερα.': 'Press the heart on products you want to save for later.',
    'Στο καλάθι': 'Add to cart',
    'Αφαίρεση': 'Remove',
    'Προσθήκη': 'Add',
    'Επεξεργαστές για κάθε PC build': 'Processors for every PC build',
    'Προσφορές': 'Offers',
    'Ποσότητα': 'Quantity',
    'Μείωση ποσότητας': 'Decrease quantity',
    'Αύξηση ποσότητας': 'Increase quantity',
    'τεμ.': 'each',
    'Σύνοψη καλαθιού': 'Cart summary',
    'Στοιχεία παράδοσης και πληρωμής': 'Delivery and payment details',
    'Δεν υπάρχουν προϊόντα για ολοκλήρωση': 'There are no products to checkout',
    'Πρόσθεσε προϊόντα στο καλάθι πριν συνεχίσεις.': 'Add products to your cart before continuing.',
    'Όνομα': 'First name',
    'Επώνυμο': 'Last name',
    'Τηλέφωνο': 'Phone',
    'Διεύθυνση': 'Address',
    'Προαιρετική τοποθεσία παράδοσης': 'Optional delivery location',
    'Αντικαταβολή': 'Cash on delivery',
    'Πληρωμή με κάρτα': 'Pay by card',
    'Συνέχεια': 'Continue',
    'Σύνοψη': 'Summary',
    'Στοιχεία κάρτας': 'Card details',
    'Ονοματεπώνυμο κατόχου': 'Cardholder name',
    'Αριθμός κάρτας': 'Card number',
    'Μήνας': 'Month',
    'Έτος': 'Year',
    'Ολοκλήρωση πληρωμής': 'Complete payment',
    'Η παραγγελία καταχωρήθηκε επιτυχώς': 'Your order was placed successfully',
    'Στείλαμε email επιβεβαίωσης στη διεύθυνση που καταχώρησες.': 'We sent a confirmation email to the address you entered.',
    'Αριθμός παραγγελίας': 'Order number',
    'Επιστροφή στο κατάστημα': 'Back to store',
    'Οι παραγγελίες μου': 'My orders',
    'Δεν υπάρχουν παραγγελίες': 'There are no orders',
    'Οι παραγγελίες που γίνονται ενώ είσαι συνδεδεμένος θα εμφανίζονται εδώ.': 'Orders placed while you are signed in will appear here.',
    'Παραγγελία': 'Order',
    'Ακύρωση παραγγελίας': 'Cancel order',
    'Σύνδεση εξαρτήματος': 'Connection',
    'Αισθητήρας': 'Sensor',
    'Χρήση': 'Use',
    'Διάταξη': 'Layout',
    'Ισχύς': 'Power',
    'Τύπος': 'Type',
    'Συμβατότητα': 'Compatibility',
    'Ανάδραση': 'Feedback',
    'Πλήκτρα': 'Keys',
    'Σχεδίαση': 'Design',
    'Μικρόφωνο': 'Microphone',
    'Διαγώνιος': 'Size',
    'Ανάλυση': 'Resolution',
    'Συχνότητα': 'Refresh rate',
    'Πάνελ': 'Panel',
    'Χωρητικότητα': 'Capacity',
    'Κλάση': 'Class',
    'Δίσκος': 'Drive',
    'Θύρα': 'Port',
    'Μέσο': 'Media',
    'Εγγραφή': 'Write speed',
    'Τοποθέτηση': 'Mounting',
    'Τεμάχια': 'Pieces',
    'Μέγεθος': 'Size',
    'Μνήμη': 'Memory',
    'Απόδοση': 'Efficiency',
    'Καλώδια': 'Cables',
    'Πρότυπο': 'Standard',
    'Έλεγχος': 'Control',
    'Φωτισμός': 'Lighting',
    'Ταχύτητα': 'Speed',
    'Μητρικές': 'Motherboards',
    'Χρώμα': 'Color',
    'Ανάγνωση': 'Read speed',
    'Πυρήνες': 'Cores',
    'Αναζήτηση παραγγελίας': 'Track order',
    'Κωδικός παραγγελίας': 'Order code',
    'Προβολή παραγγελίας': 'View order',
    'Μοναδικός κωδικός παραγγελίας': 'Unique order code',
    'Προϊόντα βάσης': 'Database products',
    'Ενεργές παραγγελίες': 'Active orders',
    'Προϊόντα προβολής': 'Displayed products',
    'Προϊόντα': 'Products',
    'Δεν υπάρχουν ενεργές παραγγελίες': 'There are no active orders',
    'Φίλτρο ονοματεπωνύμου': 'Customer name filter',
    'Αναζήτηση πελάτη': 'Search customer',
    'Καθαρισμός φίλτρου': 'Clear filter',
    'Δεν βρέθηκαν παραγγελίες για αυτό το ονοματεπώνυμο': 'No orders were found for this customer name',
    'Πελάτης': 'Customer',
    'Ακύρωση': 'Cancel',
    'Νέα εγγραφή': 'New entry',
    'Όνομα brand': 'Brand name',
    'Logo path (προαιρετικό)': 'Logo path (optional)',
    'Καταχώριση brand': 'Create brand',
    'Κατηγορία προϊόντων': 'Product category',
    'Όνομα κατηγορίας': 'Category name',
    'Γονική κατηγορία (προαιρετικό)': 'Parent category (optional)',
    'Χωρίς γονική κατηγορία': 'No parent category',
    'Καταχώριση κατηγορίας': 'Create category',
    'Προϊόν': 'Product',
    'Όνομα προϊόντος': 'Product name',
    'Κατηγορία': 'Category',
    'Επιλογή brand': 'Select brand',
    'Επιλογή κατηγορίας': 'Select category',
    'Τιμή (λεπτά)': 'Price (cents)',
    'Προσφορά (λεπτά)': 'Sale price (cents)',
    'Προσθήκη spec': 'Add spec',
    'Τιμή': 'Price',
    'Καταχώριση προϊόντος': 'Create product',
    'Κατηγορία προϊόντος': 'Product category',
    'Εικόνα': 'Image',
    'Προσφορά': 'Sale',
    'Ενέργεια': 'Action',
    'Αποθήκευση': 'Save',
    'Διαγραφή προϊόντος': 'Delete product',
    'Διαγραφή αποθηκευμένης σύνθεσης': 'Delete saved build',
    'Κλείσιμο μηνύματος': 'Close message',
    'Αφαίρεση spec': 'Remove spec',
    'Καταχωρισμένα brands': 'Registered brands',
    'Καταχωρισμένες κατηγορίες': 'Registered categories',
    'Προβολή dashboard': 'Dashboard view',
    'Επιλογή γλώσσας': 'Language selection',
    'Ενέργειες χρήστη': 'User actions',
    'Κύρια πλοήγηση': 'Main navigation',
};

const t = (text) => language.value === 'EN' ? (translations[text] ?? text) : text;
const uiText = (greek, english) => language.value === 'EN' ? english : greek;
const specT = (text) => {
    if (language.value === 'EN' && text === 'Σύνδεση') {
        return 'Connection';
    }

    return t(text);
};

watch(language, (nextLanguage) => {
    localStorage.setItem('specsStoreLanguage', nextLanguage);
});

watch(searchQuery, () => {
    window.clearTimeout(searchPreviewTimeout);

    searchPreviewTimeout = window.setTimeout(loadSearchPreview, 220);
});

const mainCategoryItems = computed(() => [
    {
        label: t('Εξαρτήματα'),
        items: [
            { label: t('Επεξεργαστές'), url: '/categories/processors' },
            { label: t('Κάρτες γραφικών'), url: '/categories/graphics-cards' },
            { label: t('Αποθήκευση'), url: '/categories/storage' },
            { label: t('Κουτιά'), url: '/categories/cases' },
            { label: t('Μνήμες RAM'), url: '/categories/ram' },
            { label: t('Μητρικές Κάρτες'), url: '/categories/motherboards' },
            { label: t('Τροφοδοτικά'), url: '/categories/power-supplies' },
            { label: t('Ανεμιστηράκια'), url: '/categories/fans' },
        ],
    },
    {
        label: t('Αποθήκευση'),
        items: [
            { label: t('Εξωτερικοί Δίσκοι'), url: '/categories/external-drives' },
            { label: t('Δίσκοι SSD & HDD'), url: '/categories/ssd-hdd' },
            { label: 'USB sticks', url: '/categories/usb-sticks' },
            { label: t('Κάρτες Μνήμης'), url: '/categories/memory-cards' },
            { label: t('Θήκες σκληρών δίσκων'), url: '/categories/drive-enclosures' },
            { label: 'CD-DVD', url: '/categories/cd-dvd' },
        ],
    },
    {
        label: t('Οθόνες & Περιφερειακά'),
        items: [
            { label: t('Πληκτρολόγια'), url: '/categories/keyboards' },
            { label: t('Ακουστικά'), url: '/categories/headsets' },
            { label: t('Οθόνες'), url: '/categories/monitors' },
            { label: t('Ποντίκια'), url: '/categories/mice' },
            { label: t('Ηχεία'), url: '/categories/speakers' },
            { label: 'Joysticks', url: '/categories/joysticks' },
        ],
    },
]);

const buildSpecs = [
    ['CPU', 'Ryzen 7 7800X3D'],
    ['GPU', 'RTX 4070 Super 12GB'],
    ['RAM', '32GB DDR5 6000'],
    ['Storage', '2TB NVMe Gen4'],
];

const builderSlots = computed(() => [
    { key: 'cpu', label: 'CPU', empty: t('Επίλεξε επεξεργαστή') },
    { key: 'motherboard', label: 'Motherboard', empty: t('Επίλεξε μητρική') },
    { key: 'ram', label: 'RAM', empty: t('Επίλεξε μνήμη') },
    { key: 'gpu', label: 'GPU', empty: t('Επίλεξε κάρτα γραφικών') },
    { key: 'storage', label: 'Storage', empty: t('Επίλεξε δίσκο') },
    { key: 'case', label: 'Case', empty: t('Επίλεξε κουτί') },
    { key: 'psu', label: 'PSU', empty: t('Επίλεξε τροφοδοτικό') },
    { key: 'fans', label: 'Fans', empty: t('Προαιρετικά ανεμιστηράκια') },
]);

const topBrands = [
    { name: 'NVIDIA', logo: '/images/brands/nvidia.svg' },
    { name: 'Intel', logo: '/images/brands/intel.svg' },
    { name: 'AMD', logo: '/images/brands/amd.svg' },
    { name: 'ASRock', logo: '/images/brands/asrock.svg' },
    { name: 'ASUS', logo: '/images/brands/asus.svg' },
    { name: 'MSI', logo: '/images/brands/msi.svg' },
    { name: 'Corsair', logo: '/images/brands/corsair.svg' },
    { name: 'Kingston', logo: '/images/brands/kingston.png' },
    { name: 'Razer', logo: '/images/brands/razer.svg' },
    { name: 'Samsung', logo: '/images/brands/samsung.svg' },
];

const toggleMainMenu = () => {
    isMainMenuOpen.value = !isMainMenuOpen.value;
};

const signOut = async () => {
    if (isSigningOut.value) {
        return;
    }

    isSigningOut.value = true;

    const response = await fetch('/logout', {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    });

    if (response.ok) {
        const signedOutResponse = await response.json();

        window.location.assign(signedOutResponse.redirect ?? '/');

        return;
    }

    isSigningOut.value = false;
};

const loadCart = async () => {
    const response = await fetch('/api/cart');
    const data = await response.json();

    cart.value = data.cart ?? cart.value;
};

const loadWishlist = async () => {
    const response = await fetch('/api/wishlist');
    const data = await response.json();

    wishlist.value = data.wishlist ?? wishlist.value;
};

const loadAccountOrders = async () => {
    if (!authUser.value) {
        return;
    }

    isLoadingAccountOrders.value = true;

    try {
        const response = await fetch('/api/account/orders', {
            headers: {
                Accept: 'application/json',
            },
        });
        const data = await response.json();

        accountOrders.value = data.orders ?? [];
    } finally {
        isLoadingAccountOrders.value = false;
    }
};

const newDraftId = () => globalThis.crypto?.randomUUID?.() ?? `${Date.now()}-${Math.random()}`;

const productSpecsToRows = (specs) => Object.entries(specs ?? {}).map(([key, value]) => ({
    id: newDraftId(),
    key,
    value: String(value ?? ''),
}));

const productSpecRowsToObject = (rows) => Object.fromEntries(
    rows
        .filter((row) => row.key.trim() !== '')
        .map((row) => [row.key.trim(), row.value ?? ''])
);

const setAdminCreateResult = (type, errors = {}, message = '') => {
    adminCreateErrors.value = { ...adminCreateErrors.value, [type]: errors };
    adminCreateMessages.value = { ...adminCreateMessages.value, [type]: message };
};

const createAdminBrand = async () => {
    adminCreating.value = { ...adminCreating.value, brand: true };
    setAdminCreateResult('brand');

    try {
        const response = await fetch('/api/admin/brands', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(adminBrandForm.value),
        });
        const data = await response.json();

        if (!response.ok) {
            setAdminCreateResult('brand', data.errors ?? {}, uiText(data.message ?? 'Η καταχώριση του brand απέτυχε.', 'Brand creation failed.'));
            return;
        }

        adminBrands.value = [...adminBrands.value, data.brand]
            .sort((firstBrand, secondBrand) => firstBrand.name.localeCompare(secondBrand.name, 'el'));
        adminNewProductForm.value.brand_id ??= data.brand.id;
        adminBrandForm.value = { name: '', logo_path: '' };
        setAdminCreateResult('brand', {}, uiText('Το brand καταχωρίστηκε.', 'The brand was created.'));
    } finally {
        adminCreating.value = { ...adminCreating.value, brand: false };
    }
};

const createAdminCategory = async () => {
    adminCreating.value = { ...adminCreating.value, category: true };
    setAdminCreateResult('category');

    try {
        const response = await fetch('/api/admin/categories', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(adminCategoryForm.value),
        });
        const data = await response.json();

        if (!response.ok) {
            setAdminCreateResult('category', data.errors ?? {}, uiText(data.message ?? 'Η καταχώριση της κατηγορίας απέτυχε.', 'Category creation failed.'));
            return;
        }

        adminCategories.value = [...adminCategories.value, data.category]
            .sort((firstCategory, secondCategory) => firstCategory.name.localeCompare(secondCategory.name, 'el'));
        adminNewProductForm.value.category_id ??= data.category.id;
        adminCategoryForm.value = { name: '', parent_id: null };
        setAdminCreateResult('category', {}, uiText('Η κατηγορία καταχωρίστηκε.', 'The category was created.'));
    } finally {
        adminCreating.value = { ...adminCreating.value, category: false };
    }
};

const adminBrandProductCount = (brandId) => adminProducts.value.filter((product) => product.brand_id === brandId).length;
const adminCategoryProductCount = (categoryId) => adminProducts.value.filter((product) => product.category_id === categoryId).length;

const deleteAdminBrand = async (brand) => {
    const productCount = adminBrandProductCount(brand.id);

    if (!window.confirm(uiText(`Να διαγραφεί οριστικά το brand «${brand.name}» και τα ${productCount} προϊόντα του;`, `Permanently delete brand "${brand.name}" and its ${productCount} products?`))) {
        return;
    }

    deletingAdminBrandIds.value = [...deletingAdminBrandIds.value, brand.id];
    setAdminCreateResult('brand');

    try {
        const response = await fetch(`/api/admin/brands/${brand.id}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            setAdminCreateResult('brand', {}, uiText(data.message ?? 'Η διαγραφή του brand απέτυχε.', 'Brand deletion failed.'));
            return;
        }

        await loadAdminProducts();
        setAdminCreateResult('brand', {}, uiText('Το brand και τα προϊόντα του διαγράφηκαν.', 'The brand and its products were deleted.'));
    } finally {
        deletingAdminBrandIds.value = deletingAdminBrandIds.value.filter((id) => id !== brand.id);
    }
};

const deleteAdminCategory = async (category) => {
    const productCount = adminCategoryProductCount(category.id);

    if (!window.confirm(uiText(`Να διαγραφεί οριστικά η κατηγορία «${category.name}», οι υποκατηγορίες και τα σχετικά προϊόντα; Η κατηγορία περιέχει άμεσα ${productCount} προϊόντα.`, `Permanently delete category "${category.name}", its subcategories, and related products? It directly contains ${productCount} products.`))) {
        return;
    }

    deletingAdminCategoryIds.value = [...deletingAdminCategoryIds.value, category.id];
    setAdminCreateResult('category');

    try {
        const response = await fetch(`/api/admin/categories/${category.id}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            setAdminCreateResult('category', {}, uiText(data.message ?? 'Η διαγραφή της κατηγορίας απέτυχε.', 'Category deletion failed.'));
            return;
        }

        await loadAdminProducts();
        setAdminCreateResult('category', {}, uiText('Η κατηγορία και όλα τα σχετικά δεδομένα διαγράφηκαν.', 'The category and all related data were deleted.'));
    } finally {
        deletingAdminCategoryIds.value = deletingAdminCategoryIds.value.filter((id) => id !== category.id);
    }
};

const addNewProductSpec = () => {
    adminNewProductForm.value.specs.push({ id: newDraftId(), key: '', value: '' });
};

const removeNewProductSpec = (specRow) => {
    adminNewProductForm.value.specs = adminNewProductForm.value.specs.filter((row) => row.id !== specRow.id);
};

const createAdminProduct = async () => {
    adminCreating.value = { ...adminCreating.value, product: true };
    setAdminCreateResult('product');

    try {
        const response = await fetch('/api/admin/products', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                ...adminNewProductForm.value,
                price_cents: Number(adminNewProductForm.value.price_cents),
                sale_price_cents: adminNewProductForm.value.sale_price_cents === null || adminNewProductForm.value.sale_price_cents === ''
                    ? null
                    : Number(adminNewProductForm.value.sale_price_cents),
                stock: Number(adminNewProductForm.value.stock),
                specs: productSpecRowsToObject(adminNewProductForm.value.specs),
            }),
        });
        const data = await response.json();

        if (!response.ok) {
            setAdminCreateResult('product', data.errors ?? {}, uiText(data.message ?? 'Η καταχώριση του προϊόντος απέτυχε.', 'Product creation failed.'));
            return;
        }

        adminProducts.value = [...adminProducts.value, data.product];
        adminProductDrafts.value = {
            ...adminProductDrafts.value,
            [data.product.id]: {
                name: data.product.name,
                sku: data.product.sku,
                brand_id: data.product.brand_id,
                category_id: data.product.category_id,
                image_path: data.product.image_path,
                price_cents: data.product.price_cents,
                sale_price_cents: data.product.sale_price_cents,
                stock: data.product.stock,
                specs: productSpecsToRows(data.product.specs),
            },
        };
        selectedAdminCategory.value = data.product.category;
        adminNewProductForm.value = {
            name: '',
            sku: '',
            brand_id: data.product.brand_id,
            category_id: data.product.category_id,
            image_path: '',
            price_cents: 0,
            sale_price_cents: null,
            stock: 0,
            specs: [],
        };
        setAdminCreateResult('product', {}, uiText('Το προϊόν καταχωρίστηκε.', 'The product was created.'));
    } finally {
        adminCreating.value = { ...adminCreating.value, product: false };
    }
};

const loadAdminProducts = async () => {
    if (!authUser.value?.is_admin) {
        return;
    }

    isLoadingAdminProducts.value = true;

    try {
        const response = await fetch('/api/admin/products', {
            headers: {
                Accept: 'application/json',
            },
        });
        const data = await response.json();

        adminProducts.value = data.products ?? [];
        adminBrands.value = data.brands ?? [];
        adminCategories.value = data.categories ?? [];
        if (!adminBrands.value.some((brand) => brand.id === adminNewProductForm.value.brand_id)) {
            adminNewProductForm.value.brand_id = adminBrands.value[0]?.id ?? null;
        }
        if (!adminCategories.value.some((category) => category.id === adminNewProductForm.value.category_id)) {
            adminNewProductForm.value.category_id = adminCategories.value[0]?.id ?? null;
        }
        selectedAdminCategory.value = [...new Set(adminProducts.value
            .map((product) => product.category)
            .filter(Boolean))]
            .sort((firstCategory, secondCategory) => firstCategory.localeCompare(secondCategory, 'el'))[0] ?? 'all';
        adminProductDrafts.value = Object.fromEntries(adminProducts.value.map((product) => [
            product.id,
            {
                name: product.name,
                sku: product.sku,
                brand_id: product.brand_id,
                category_id: product.category_id,
                image_path: product.image_path,
                price_cents: product.price_cents,
                sale_price_cents: product.sale_price_cents,
                stock: product.stock,
                specs: productSpecsToRows(product.specs),
            },
        ]));
    } finally {
        isLoadingAdminProducts.value = false;
    }
};

const loadAdminOrders = async () => {
    if (!authUser.value?.is_admin) {
        return;
    }

    isLoadingAdminOrders.value = true;

    try {
        const response = await fetch('/api/admin/orders', {
            headers: { Accept: 'application/json' },
        });
        const data = await response.json();

        adminOrders.value = data.orders ?? [];
    } finally {
        isLoadingAdminOrders.value = false;
    }
};

const cancelAdminOrder = async (order) => {
    if (!window.confirm(language.value === 'EN' ? `Cancel order #${order.id}?` : `Να ακυρωθεί η παραγγελία #${order.id};`)) {
        return;
    }

    cancellingAdminOrderIds.value = [...cancellingAdminOrderIds.value, order.id];

    try {
        const response = await fetch(`/api/admin/orders/${order.id}/cancel`, {
            method: 'PATCH',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });

        if (response.ok) {
            adminOrders.value = adminOrders.value.filter((currentOrder) => currentOrder.id !== order.id);
        }
    } finally {
        cancellingAdminOrderIds.value = cancellingAdminOrderIds.value.filter((id) => id !== order.id);
    }
};

const findOrder = async () => {
    trackingError.value = '';
    trackedOrder.value = null;
    isTrackingOrder.value = true;

    try {
        const response = await fetch('/api/orders/track', {
            method: 'POST',
            headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ tracking_code: trackingCode.value.trim().toUpperCase() }),
        });
        const data = await response.json();

        if (!response.ok) {
            trackingError.value = response.status === 404
                ? (language.value === 'EN' ? 'No order was found with this code.' : 'Δεν βρέθηκε παραγγελία με αυτόν τον κωδικό.')
                : (language.value === 'EN' ? 'The code is invalid.' : (data.message ?? 'Ο κωδικός δεν είναι έγκυρος.'));
            return;
        }

        trackedOrder.value = data.order;
    } finally {
        isTrackingOrder.value = false;
    }
};

const saveAdminProduct = async (product) => {
    const draft = adminProductDrafts.value[product.id];

    if (!draft) {
        return;
    }

    const specs = productSpecRowsToObject(draft.specs ?? []);

    savingAdminProductIds.value = [...savingAdminProductIds.value, product.id];
    adminProductMessages.value = {
        ...adminProductMessages.value,
        [product.id]: '',
    };

    try {
        const response = await fetch(`/api/admin/products/${product.id}`, {
            method: 'PUT',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                name: draft.name,
                sku: draft.sku,
                brand_id: draft.brand_id,
                category_id: draft.category_id,
                image_path: draft.image_path,
                price_cents: Number(draft.price_cents),
                sale_price_cents: draft.sale_price_cents === null || draft.sale_price_cents === '' ? null : Number(draft.sale_price_cents),
                stock: Number(draft.stock),
                specs,
            }),
        });
        const data = await response.json();

        if (!response.ok) {
            adminProductMessages.value = {
                ...adminProductMessages.value,
                [product.id]: uiText(data.message ?? 'Η αποθήκευση απέτυχε.', 'Saving failed.'),
            };

            return;
        }

        adminProducts.value = adminProducts.value.map((currentProduct) => currentProduct.id === product.id ? data.product : currentProduct);
        adminProductDrafts.value = {
            ...adminProductDrafts.value,
            [product.id]: {
                name: data.product.name,
                sku: data.product.sku,
                brand_id: data.product.brand_id,
                category_id: data.product.category_id,
                image_path: data.product.image_path,
                price_cents: data.product.price_cents,
                sale_price_cents: data.product.sale_price_cents,
                stock: data.product.stock,
                specs: productSpecsToRows(data.product.specs),
            },
        };
        adminProductMessages.value = {
            ...adminProductMessages.value,
            [product.id]: uiText('Αποθηκεύτηκε.', 'Saved.'),
        };
    } finally {
        savingAdminProductIds.value = savingAdminProductIds.value.filter((id) => id !== product.id);
    }
};

const deleteAdminProduct = async (product) => {
    if (!window.confirm(uiText(`Να διαγραφεί οριστικά το προϊόν «${product.name}»;`, `Permanently delete product "${product.name}"?`))) {
        return;
    }

    deletingAdminProductIds.value = [...deletingAdminProductIds.value, product.id];
    adminProductMessages.value = { ...adminProductMessages.value, [product.id]: '' };

    try {
        const response = await fetch(`/api/admin/products/${product.id}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        if (!response.ok) {
            const data = await response.json();
            adminProductMessages.value = {
                ...adminProductMessages.value,
                [product.id]: uiText(data.message ?? 'Η διαγραφή απέτυχε.', 'Deletion failed.'),
            };
            return;
        }

        adminProducts.value = adminProducts.value.filter((currentProduct) => currentProduct.id !== product.id);
        delete adminProductDrafts.value[product.id];
        delete adminProductMessages.value[product.id];

        if (!adminProducts.value.some((currentProduct) => currentProduct.category === selectedAdminCategory.value)) {
            selectedAdminCategory.value = [...new Set(adminProducts.value
                .map((currentProduct) => currentProduct.category)
                .filter(Boolean))]
                .sort((firstCategory, secondCategory) => firstCategory.localeCompare(secondCategory, 'el'))[0] ?? 'all';
        }
    } finally {
        deletingAdminProductIds.value = deletingAdminProductIds.value.filter((id) => id !== product.id);
    }
};

const addAdminProductSpec = (product) => {
    const draft = adminProductDrafts.value[product.id];

    if (!draft) {
        return;
    }

    draft.specs.push({
        id: newDraftId(),
        key: '',
        value: '',
    });
};

const removeAdminProductSpec = (product, specRow) => {
    const draft = adminProductDrafts.value[product.id];

    if (!draft) {
        return;
    }

    draft.specs = draft.specs.filter((row) => row.id !== specRow.id);
};

const cancelOrder = async (order) => {
    const response = await fetch(`/api/account/orders/${order.id}/cancel`, {
        method: 'PATCH',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    });

    if (response.ok) {
        const data = await response.json();
        const updatedOrder = data.order;

        accountOrders.value = accountOrders.value.map((currentOrder) => {
            return currentOrder.id === updatedOrder.id ? updatedOrder : currentOrder;
        });
    }
};

const addToCart = async (product) => {
    cartStockError.value = '';
    const response = await fetch('/api/cart', {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            product_id: product.id,
            quantity: 1,
        }),
    });

    const data = await response.json();

    if (response.ok) {

        cart.value = data.cart ?? cart.value;

        return;
    }

    cartStockError.value = data.message ?? 'Η ζητούμενη ποσότητα δεν είναι διαθέσιμη.';
};

const toggleWishlist = async (product) => {
    const isWishlisted = wishlist.value.product_ids.includes(product.id);
    const response = await fetch(isWishlisted ? `/api/wishlist/${product.id}` : '/api/wishlist', {
        method: isWishlisted ? 'DELETE' : 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: isWishlisted ? undefined : JSON.stringify({
            product_id: product.id,
        }),
    });

    if (response.ok) {
        const data = await response.json();

        wishlist.value = data.wishlist ?? wishlist.value;
    }
};

const loadBuilderProducts = async () => {
    isLoadingBuilderProducts.value = true;

    try {
        const response = await fetch('/api/pc-builder/products');
        const data = await response.json();

        builderProducts.value = data.products ?? {};
    } finally {
        isLoadingBuilderProducts.value = false;
    }
};

const selectBuilderProduct = (slot, product) => {
    builderSelection.value[slot] = product;
    builderSaveMessage.value = '';
};

const clearBuilderProduct = (slot) => {
    builderSelection.value[slot] = null;
    builderSaveMessage.value = '';
};

const clearBuilderSelection = () => {
    builderSelection.value = {
        cpu: null,
        motherboard: null,
        ram: null,
        gpu: null,
        storage: null,
        case: null,
        psu: null,
        fans: null,
    };
    selectedSavedBuild.value = null;
    loadedSavedBuild.value = null;
    buildName.value = '';
    builderSaveMessage.value = '';
};

const normalizeFormFactor = (value) => String(value ?? '')
    .toLowerCase()
    .replace('micro-atx', 'matx')
    .replace('micro atx', 'matx')
    .replace('m-atx', 'matx');

const caseSupportsMotherboard = (pcCase, motherboard) => {
    if (!pcCase || !motherboard) {
        return true;
    }

    const support = normalizeFormFactor(pcCase.specs.motherboard_support);
    const board = normalizeFormFactor(motherboard.specs.form_factor);

    if (!support || !board) {
        return true;
    }

    if (support.includes('e-atx')) {
        return ['e-atx', 'atx', 'matx', 'mini-itx'].some((size) => board.includes(size));
    }

    if (support.includes('atx')) {
        return ['atx', 'matx', 'mini-itx'].some((size) => board.includes(size));
    }

    if (support.includes('matx')) {
        return ['matx', 'mini-itx'].some((size) => board.includes(size));
    }

    return support.includes(board);
};

const parseWatts = (value) => Number(String(value ?? '').match(/\d+/)?.[0] ?? 0);

const estimatedGpuWatts = (gpu) => {
    const chipset = String(gpu?.specs?.chipset ?? '').toLowerCase();

    if (!chipset) {
        return 0;
    }

    if (chipset.includes('5090')) {
        return 575;
    }

    if (chipset.includes('5080') || chipset.includes('7900 xtx')) {
        return 360;
    }

    if (chipset.includes('5070') || chipset.includes('9070') || chipset.includes('7900 xt')) {
        return 300;
    }

    if (chipset.includes('7800') || chipset.includes('5060 ti')) {
        return 250;
    }

    if (chipset.includes('5060') || chipset.includes('4060') || chipset.includes('7600') || chipset.includes('arc')) {
        return 180;
    }

    return 240;
};

const estimatedCpuWatts = (cpu) => {
    const name = String(cpu?.name ?? '').toLowerCase();

    if (!name) {
        return 0;
    }

    if (name.includes('ryzen 9') || name.includes('core i9') || name.includes('ultra 9')) {
        return 180;
    }

    if (name.includes('ryzen 7') || name.includes('core i7') || name.includes('ultra 7')) {
        return 140;
    }

    return 100;
};

const recommendedPsuWatts = computed(() => {
    const cpuWatts = estimatedCpuWatts(builderSelection.value.cpu);
    const gpuWatts = estimatedGpuWatts(builderSelection.value.gpu);

    if (!cpuWatts && !gpuWatts) {
        return 0;
    }

    return Math.ceil((cpuWatts + gpuWatts + 120) * 1.35 / 50) * 50;
});

const isBuilderCompatible = (slot, product) => {
    const selected = builderSelection.value;

    if (slot === 'cpu' && selected.motherboard) {
        return product.specs.socket === selected.motherboard.specs.socket;
    }

    if (slot === 'motherboard') {
        const cpuMatches = !selected.cpu || product.specs.socket === selected.cpu.specs.socket;
        const ramMatches = !selected.ram || product.specs.memory_type === selected.ram.specs.memory_type;
        const caseMatches = !selected.case || caseSupportsMotherboard(selected.case, product);

        return cpuMatches && ramMatches && caseMatches;
    }

    if (slot === 'ram' && selected.motherboard) {
        return product.specs.memory_type === selected.motherboard.specs.memory_type;
    }

    if (slot === 'case' && selected.motherboard) {
        return caseSupportsMotherboard(product, selected.motherboard);
    }

    if (slot === 'psu' && recommendedPsuWatts.value > 0) {
        return parseWatts(product.specs.wattage) >= recommendedPsuWatts.value;
    }

    return true;
};

const compatibleBuilderProducts = (slot) => {
    return (builderProducts.value[slot] ?? []).filter((product) => isBuilderCompatible(slot, product));
};

const builderSlotOptions = (slot) => builderProducts.value[slot] ?? [];

const selectedComponentIds = computed(() => {
    return builderSlots.value.reduce((components, slot) => ({
        ...components,
        [slot.key]: builderSelection.value[slot.key]?.id ?? null,
    }), {});
});

const builderTotal = computed(() => {
    return Object.values(builderSelection.value).reduce((total, product) => {
        if (!product) {
            return total;
        }

        return total + (product.sale_price ?? product.price ?? 0);
    }, 0);
});

const formattedBuilderTotal = computed(() => new Intl.NumberFormat(language.value === 'EN' ? 'en-US' : 'el-GR', {
    style: 'currency',
    currency: 'EUR',
}).format(builderTotal.value));

const selectedBuilderCount = computed(() => Object.values(builderSelection.value).filter(Boolean).length);

const adminCategoryOptions = computed(() => [
    { label: language.value === 'EN' ? 'All categories' : 'Όλες οι κατηγορίες', value: 'all' },
    ...[...new Set(adminProducts.value.map((product) => product.category).filter(Boolean))]
        .sort((firstCategory, secondCategory) => firstCategory.localeCompare(secondCategory, 'el'))
        .map((category) => ({ label: category, value: category })),
]);

const filteredAdminProducts = computed(() => {
    if (selectedAdminCategory.value === 'all') {
        return adminProducts.value;
    }

    return adminProducts.value.filter((product) => product.category === selectedAdminCategory.value);
});

const normalizeSearchText = (value) => String(value ?? '')
    .normalize('NFD')
    .replace(/\p{Diacritic}/gu, '')
    .toLocaleLowerCase('el');

const filteredAdminOrders = computed(() => {
    const query = normalizeSearchText(adminOrderNameFilter.value.trim());

    if (!query) {
        return adminOrders.value;
    }

    return adminOrders.value.filter((order) => normalizeSearchText(order.customer_name).includes(query));
});

const groupedAdminOrders = computed(() => Object.values(filteredAdminOrders.value.reduce((groups, order) => {
    const key = `${order.last_name}\u0000${order.first_name}`;

    groups[key] ??= { customer_name: order.customer_name, orders: [] };
    groups[key].orders.push(order);

    return groups;
}, {})));

const builderCompatibilityMessages = computed(() => {
    const selected = builderSelection.value;
    const messages = [];

    if (selected.cpu && selected.motherboard && selected.cpu.specs.socket !== selected.motherboard.specs.socket) {
        messages.push(language.value === 'EN'
            ? `CPU and Motherboard: "${selected.cpu.name}" (${selected.cpu.specs.socket}) does not match "${selected.motherboard.name}" (${selected.motherboard.specs.socket}).`
            : `CPU και Motherboard: "${selected.cpu.name}" (${selected.cpu.specs.socket}) δεν ταιριάζει με "${selected.motherboard.name}" (${selected.motherboard.specs.socket}).`);
    }

    if (selected.ram && selected.motherboard && selected.ram.specs.memory_type !== selected.motherboard.specs.memory_type) {
        messages.push(language.value === 'EN'
            ? `RAM and Motherboard: "${selected.ram.name}" (${selected.ram.specs.memory_type}) does not match "${selected.motherboard.name}" (${selected.motherboard.specs.memory_type}).`
            : `RAM και Motherboard: "${selected.ram.name}" (${selected.ram.specs.memory_type}) δεν ταιριάζει με "${selected.motherboard.name}" (${selected.motherboard.specs.memory_type}).`);
    }

    if (selected.case && selected.motherboard && !caseSupportsMotherboard(selected.case, selected.motherboard)) {
        messages.push(language.value === 'EN'
            ? `Case and Motherboard: "${selected.case.name}" supports ${selected.case.specs.motherboard_support}, but "${selected.motherboard.name}" is ${selected.motherboard.specs.form_factor}.`
            : `Case και Motherboard: "${selected.case.name}" υποστηρίζει ${selected.case.specs.motherboard_support}, αλλά η "${selected.motherboard.name}" είναι ${selected.motherboard.specs.form_factor}.`);
    }

    if (selected.psu && recommendedPsuWatts.value > 0 && parseWatts(selected.psu.specs.wattage) < recommendedPsuWatts.value) {
        messages.push(language.value === 'EN'
            ? `PSU: "${selected.psu.name}" (${selected.psu.specs.wattage}) is below the recommended ${recommendedPsuWatts.value}W for this CPU/GPU.`
            : `PSU: "${selected.psu.name}" (${selected.psu.specs.wattage}) είναι κάτω από τα προτεινόμενα ${recommendedPsuWatts.value}W για CPU/GPU.`);
    }

    return messages;
});

const builderProductCompatibilityIssues = (slot) => {
    const selected = builderSelection.value;
    const product = selected[slot];

    if (!product) {
        return [];
    }

    return builderCompatibilityMessages.value.filter((message) => {
        return message.includes(product.name);
    });
};

const isBuilderComplete = computed(() => ['cpu', 'motherboard', 'ram', 'gpu', 'storage', 'case', 'psu']
    .every((slot) => builderSelection.value[slot]));

const hasLoadedBuildChanges = computed(() => {
    if (!loadedSavedBuild.value) {
        return false;
    }

    return builderSlots.value.some((slot) => {
        const savedProductId = loadedSavedBuild.value.component_product_ids?.[slot.key] ?? null;

        return (selectedComponentIds.value[slot.key] ?? null) !== savedProductId;
    });
});

const defaultBuildName = () => {
    const date = new Intl.DateTimeFormat('el-GR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date());

    return `${language.value === 'EN' ? 'PC build' : 'PC σύνθεση'} ${date}`;
};

const loadSavedBuilds = async () => {
    if (!authUser.value) {
        savedBuilds.value = [];

        return;
    }

    isLoadingSavedBuilds.value = true;

    try {
        const response = await fetch('/api/pc-builder/builds', {
            headers: {
                Accept: 'application/json',
            },
        });
        const data = await response.json();

        savedBuilds.value = data.builds ?? [];
    } finally {
        isLoadingSavedBuilds.value = false;
    }
};

const loadSavedBuild = (build) => {
    if (!build) {
        return;
    }

    builderSlots.value.forEach((slot) => {
        builderSelection.value[slot.key] = build.components?.[slot.key] ?? null;
    });
    selectedSavedBuild.value = build;
    loadedSavedBuild.value = build;
    buildName.value = build.name;
    builderSaveMessage.value = language.value === 'EN'
        ? `Loaded build "${build.name}".`
        : `Φορτώθηκε η σύνθεση "${build.name}".`;
};

const deleteSavedBuild = async () => {
    const build = selectedSavedBuild.value;

    if (!build || !window.confirm(language.value === 'EN' ? `Permanently delete build "${build.name}"?` : `Να διαγραφεί οριστικά η σύνθεση «${build.name}»;`)) {
        return;
    }

    isDeletingSavedBuild.value = true;
    builderSaveMessage.value = '';

    try {
        const response = await fetch(`/api/pc-builder/builds/${build.id}`, {
            method: 'DELETE',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });

        if (!response.ok) {
            const data = await response.json();
            builderSaveMessage.value = language.value === 'EN' ? 'The build could not be deleted.' : (data.message ?? 'Η διαγραφή της σύνθεσης απέτυχε.');
            return;
        }

        savedBuilds.value = savedBuilds.value.filter((savedBuild) => savedBuild.id !== build.id);
        selectedSavedBuild.value = null;
        loadedSavedBuild.value = null;
        builderSaveMessage.value = language.value === 'EN' ? 'The saved build was deleted.' : 'Η αποθηκευμένη σύνθεση διαγράφηκε.';
    } finally {
        isDeletingSavedBuild.value = false;
    }
};

const saveBuild = async (replaceExisting = false) => {
    if (!authUser.value || !isBuilderComplete.value || builderCompatibilityMessages.value.length > 0 || isSavingBuild.value) {
        return;
    }

    const targetBuild = replaceExisting ? loadedSavedBuild.value : null;
    const name = buildName.value.trim() || targetBuild?.name || defaultBuildName();

    isSavingBuild.value = true;
    builderSaveMessage.value = '';

    try {
        const response = await fetch(targetBuild ? `/api/pc-builder/builds/${targetBuild.id}` : '/api/pc-builder/builds', {
            method: targetBuild ? 'PUT' : 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                name,
                components: selectedComponentIds.value,
            }),
        });
        const data = await response.json();

        if (!response.ok) {
            builderSaveMessage.value = data.errors?.components?.[0] ?? data.message ?? (language.value === 'EN' ? 'The build was not saved.' : 'Η σύνθεση δεν αποθηκεύτηκε.');

            return;
        }

        await loadSavedBuilds();
        loadedSavedBuild.value = data.build;
        selectedSavedBuild.value = data.build;
        buildName.value = data.build.name;
        builderSaveMessage.value = targetBuild
            ? (language.value === 'EN' ? 'The existing build was replaced.' : 'Η υπάρχουσα σύνθεση αντικαταστάθηκε.')
            : (language.value === 'EN' ? 'The build was saved.' : 'Η σύνθεση αποθηκεύτηκε.');
    } finally {
        isSavingBuild.value = false;
    }
};

const addBuildToCart = async () => {
    const selectedProducts = Object.values(builderSelection.value).filter(Boolean);
    cartStockError.value = '';
    const response = await fetch('/api/cart/build', {
        method: 'POST',
        headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ product_ids: selectedProducts.map((product) => product.id) }),
    });
    const data = await response.json();

    if (!response.ok) {
        cartStockError.value = language.value === 'EN'
            ? 'The build cannot be added because one or more products are unavailable.'
            : (data.errors?.cart?.join(' ') ?? data.message ?? 'Η σύνθεση δεν μπορεί να προστεθεί στο καλάθι.');
        return;
    }

    cart.value = data.cart ?? cart.value;
};

const removeFromWishlist = async (item) => {
    const response = await fetch(`/api/wishlist/${item.id}`, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    });

    if (response.ok) {
        const data = await response.json();

        wishlist.value = data.wishlist ?? wishlist.value;
    }
};

const removeFromCart = async (item) => {
    const response = await fetch(`/api/cart/${item.id}`, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    });

    if (response.ok) {
        const data = await response.json();

        cart.value = data.cart ?? cart.value;
    }
};

const updateCartQuantity = async (item, quantity) => {
    cartStockError.value = '';
    const nextQuantity = Math.min(Math.max(Number(quantity) || 1, 1), 99);
    const response = await fetch(`/api/cart/${item.id}`, {
        method: 'PATCH',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            quantity: nextQuantity,
        }),
    });

    const data = await response.json();

    if (response.ok) {

        cart.value = data.cart ?? cart.value;

        return;
    }

    cartStockError.value = data.message ?? 'Η ζητούμενη ποσότητα δεν είναι διαθέσιμη.';
};

const proceedToCheckout = async () => {
    cartStockError.value = '';
    isValidatingCartStock.value = true;

    try {
        const response = await fetch('/api/cart/validate-stock', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });
        const data = await response.json();

        if (!response.ok) {
            cartStockError.value = data.message ?? 'Κάποια ποσότητα του καλαθιού δεν είναι διαθέσιμη.';

            return;
        }

        window.location.assign('/checkout');
    } finally {
        isValidatingCartStock.value = false;
    }
};

const placeDeliveryPin = (event) => {
    const bounds = event.currentTarget.getBoundingClientRect();
    const x = (event.clientX - bounds.left) / bounds.width;
    const y = (event.clientY - bounds.top) / bounds.height;

    checkoutForm.value.delivery_latitude = Number((37.99 + (0.5 - y) * 0.2).toFixed(7));
    checkoutForm.value.delivery_longitude = Number((23.73 + (x - 0.5) * 0.2).toFixed(7));
    deliveryPin.value = {
        x: Number((x * 100).toFixed(2)),
        y: Number((y * 100).toFixed(2)),
    };
};

const submitCheckout = async () => {
    checkoutErrors.value = {};
    isSubmittingCheckout.value = true;

    try {
        const response = await fetch('/api/checkout', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(checkoutForm.value),
        });

        const data = await response.json();

        if (response.status === 422) {
            checkoutErrors.value = data.errors ?? { form: [data.message ?? 'Η παραγγελία δεν μπορεί να ολοκληρωθεί.'] };

            return;
        }

        if (response.ok) {
            window.location.assign(data.redirect ?? '/checkout/success');
        }
    } finally {
        isSubmittingCheckout.value = false;
    }
};

const submitCard = async () => {
    cardErrors.value = {};
    isSubmittingCard.value = true;

    try {
        const response = await fetch('/api/checkout/card', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(cardForm.value),
        });

        const data = await response.json();

        if (response.status === 422) {
            cardErrors.value = data.errors ?? { form: [data.message ?? 'Τα στοιχεία κάρτας δεν έγιναν αποδεκτά.'] };

            if (data.redirect) {
                window.location.assign(data.redirect);
            }

            return;
        }

        if (response.ok) {
            window.location.assign(data.redirect ?? '/checkout/success');
        }
    } finally {
        isSubmittingCard.value = false;
    }
};

const submitSearch = () => {
    const query = searchQuery.value.trim();

    if (!query) {
        window.location.assign('/');

        return;
    }

    window.location.assign(`/search?q=${encodeURIComponent(query)}`);
};

const goHome = () => {
    searchQuery.value = '';
    searchPreviewProducts.value = [];
    isSearchPreviewOpen.value = false;
    window.location.assign('/');
};

const loadSearchPreview = async () => {
    const query = searchQuery.value.trim();

    if (!query) {
        searchPreviewProducts.value = [];
        isSearchPreviewOpen.value = false;

        return;
    }

    isLoadingSearchPreview.value = true;

    try {
        const response = await fetch(`/api/search-products?q=${encodeURIComponent(query)}`);
        const data = await response.json();

        searchPreviewProducts.value = data.products ?? [];
        isSearchPreviewOpen.value = true;
    } finally {
        isLoadingSearchPreview.value = false;
    }
};

const filteredProducts = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();

    if (!query) {
        return products.value;
    }

    return products.value.filter((product) => product.name.toLowerCase().includes(query));
});

const productSectionTitle = computed(() => {
    if (isSearchPage) {
        return searchQuery.value.trim()
            ? `${t('Αποτελέσματα αναζήτησης')}: "${searchQuery.value.trim()}"`
            : t('Αποτελέσματα αναζήτησης');
    }

    if (isOffersPage) {
        return t('Προσφορές');
    }

    return language.value === 'EN'
        ? (categoryPage?.titleEn ?? categoryPage?.title ?? t('Επεξεργαστές για κάθε PC build'))
        : (categoryPage?.title ?? 'Επεξεργαστές για κάθε PC build');
});
const productSectionEyebrow = computed(() => {
    if (isSearchPage) {
        return 'Search results';
    }

    if (isOffersPage) {
        return 'Deals collection';
    }

    return categoryPage?.eyebrow ?? 'CPU collection';
});
const cartTitle = computed(() => {
    if (language.value === 'EN') {
        return cart.value.count === 1 ? '1 product' : `${cart.value.count} products`;
    }

    return cart.value.count === 1 ? '1 προϊόν' : `${cart.value.count} προϊόντα`;
});
const wishlistTitle = computed(() => {
    if (language.value === 'EN') {
        return wishlist.value.count === 1 ? '1 product' : `${wishlist.value.count} products`;
    }

    return wishlist.value.count === 1 ? '1 προϊόν' : `${wishlist.value.count} προϊόντα`;
});
const successTrackingCode = computed(() => currentPath.split('/').filter(Boolean).at(-1) ?? '');

const featuredProductSpecs = (product) => {
    if (categoryPage?.slug === 'mice') {
        return [
            ['Σύνδεση', product.specs.connection],
            ['Αισθητήρας', product.specs.sensor],
            ['Χρήση', product.specs.use_case],
        ];
    }

    if (categoryPage?.slug === 'speakers') {
        return [
            ['Διάταξη', product.specs.system_type],
            ['Σύνδεση', product.specs.connection],
            ['Ισχύς', product.specs.output_power],
        ];
    }

    if (categoryPage?.slug === 'joysticks') {
        return [
            ['Τύπος', product.specs.controller_type],
            ['Σύνδεση', product.specs.connection],
            ['Συμβατότητα', product.specs.compatibility],
            ['Ανάδραση', product.specs.feedback],
        ];
    }

    if (categoryPage?.slug === 'keyboards') {
        return [
            ['Διάταξη', product.specs.layout],
            ['Σύνδεση', product.specs.connection],
            ['Πλήκτρα', product.specs.key_type],
        ];
    }

    if (categoryPage?.slug === 'headsets') {
        return [
            ['Σύνδεση', product.specs.connection],
            ['Σχεδίαση', product.specs.form_factor],
            ['Μικρόφωνο', product.specs.microphone],
        ];
    }

    if (categoryPage?.slug === 'monitors') {
        return [
            ['Διαγώνιος', product.specs.screen_size],
            ['Ανάλυση', product.specs.resolution],
            ['Συχνότητα', product.specs.refresh_rate],
            ['Πάνελ', product.specs.panel_type],
        ];
    }

    if (categoryPage?.slug === 'memory-cards') {
        return [
            ['Χωρητικότητα', product.specs.capacity],
            ['Τύπος', product.specs.form_factor],
            ['Σύνδεση', product.specs.interface],
            ['Κλάση', product.specs.speed_class],
        ];
    }

    if (categoryPage?.slug === 'drive-enclosures') {
        return [
            ['Δίσκος', product.specs.drive_support],
            ['Σύνδεση', product.specs.interface],
            ['Θύρα', product.specs.connector],
            ['Τύπος', product.specs.enclosure_type],
        ];
    }

    if (categoryPage?.slug === 'cd-dvd') {
        return product.specs.drive_type ? [
            ['Τύπος', product.specs.drive_type],
            ['Σύνδεση', product.specs.interface],
            ['Εγγραφή', product.specs.write_speed],
            ['Τοποθέτηση', product.specs.form_factor],
        ] : [
            ['Μέσο', product.specs.format],
            ['Χωρητικότητα', product.specs.capacity],
            ['Εγγραφή', product.specs.write_speed],
            ['Τεμάχια', product.specs.quantity],
        ];
    }

    if (categoryPage?.slug === 'external-drives') {
        return [
            ['Χωρητικότητα', product.specs.capacity],
            ['Τύπος', product.specs.drive_type],
            ['Σύνδεση', product.specs.interface],
            ['Θύρα', product.specs.connector],
        ];
    }

    if (categoryPage?.slug === 'ssd-hdd') {
        return [
            ['Χωρητικότητα', product.specs.capacity],
            ['Τύπος', product.specs.drive_type],
            ['Σύνδεση', product.specs.interface],
            ['Μέγεθος', product.specs.form_factor],
        ];
    }

    if (categoryPage?.slug === 'usb-sticks') {
        return [
            ['Χωρητικότητα', product.specs.capacity],
            ['Σύνδεση', product.specs.interface],
            ['Θύρα', product.specs.connector],
            ['Σχεδίαση', product.specs.design],
        ];
    }

    if (categoryPage?.slug === 'motherboards') {
        return [
            ['Socket', product.specs.socket],
            ['Chipset', product.specs.chipset],
            ['Μέγεθος', product.specs.form_factor],
            ['Μνήμη', product.specs.memory_type],
        ];
    }

    if (categoryPage?.slug === 'power-supplies') {
        return [
            ['Ισχύς', product.specs.wattage],
            ['Απόδοση', product.specs.efficiency],
            ['Καλώδια', product.specs.modularity],
            ['Πρότυπο', product.specs.standard],
        ];
    }

    if (categoryPage?.slug === 'fans') {
        return [
            ['Μέγεθος', product.specs.size],
            ['Έλεγχος', product.specs.control],
            ['Φωτισμός', product.specs.lighting],
            ['Τεμάχια', product.specs.quantity],
        ];
    }

    if (categoryPage?.slug === 'ram') {
        return [
            ['Χωρητικότητα', `${product.specs.capacity} (${product.specs.modules})`],
            ['Τύπος', product.specs.memory_type],
            ['Ταχύτητα', product.specs.speed],
            ['Latency', product.specs.latency],
        ];
    }

    if (product.specs.case_type) {
        return [
            ['Μέγεθος', product.specs.case_type],
            ['Μητρικές', product.specs.motherboard_support],
            ['Διάταξη', product.specs.layout],
            ['Χρώμα', product.specs.color],
        ];
    }

    if (product.specs.capacity) {
        return [
            ['Χωρητικότητα', product.specs.capacity],
            ['Σύνδεση', product.specs.interface],
            ['Τύπος', product.specs.form_factor],
            ['Ανάγνωση', product.specs.read_speed],
        ];
    }

    if (product.specs.cores) {
        return [
            ['Πυρήνες', product.specs.cores],
            ['Threads', product.specs.threads],
            ['Socket', product.specs.socket],
            ['Boost', product.specs.boost_clock],
        ];
    }

    return [
        ['Chipset', product.specs.chipset],
        ['VRAM', product.specs.vram],
        ['Memory', product.specs.memory_type],
        ['Boost', product.specs.boost_clock],
    ];
};

const specLabels = {
    boost_clock: 'Boost',
    capacity: 'Χωρητικότητα',
    case_type: 'Μέγεθος',
    color: 'Χρώμα',
    connection: 'Σύνδεση',
    cores: 'Πυρήνες',
    design: 'Σχεδίαση',
    drive_support: 'Δίσκος',
    drive_type: 'Τύπος',
    efficiency: 'Απόδοση',
    feedback: 'Ανάδραση',
    form_factor: 'Σχεδίαση',
    format: 'Μέσο',
    interface: 'Σύνδεση',
    key_type: 'Πλήκτρα',
    layout: 'Διάταξη',
    lighting: 'Φωτισμός',
    memory_type: 'Μνήμη',
    microphone: 'Μικρόφωνο',
    motherboard_support: 'Μητρικές',
    output_power: 'Ισχύς',
    panel_type: 'Πάνελ',
    quantity: 'Τεμάχια',
    read_speed: 'Ανάγνωση',
    refresh_rate: 'Συχνότητα',
    resolution: 'Ανάλυση',
    screen_size: 'Διαγώνιος',
    sensor: 'Αισθητήρας',
    size: 'Μέγεθος',
    speed: 'Ταχύτητα',
    speed_class: 'Κλάση',
    system_type: 'Διάταξη',
    use_case: 'Χρήση',
    wattage: 'Ισχύς',
    write_speed: 'Εγγραφή',
};

const productSpecs = (product) => Object.entries(product.specs ?? {})
    .filter(([, value]) => value !== null && value !== undefined && value !== '')
    .map(([key, value]) => [
        specLabels[key] ?? key.replaceAll('_', ' ').replace(/^./, (character) => character.toUpperCase()),
        value,
    ]);

onMounted(async () => {
    await loadCart();
    await loadWishlist();

    if (isAccountOrdersPage) {
        await loadAccountOrders();
    }

    if (isAdminProductsPage) {
        await Promise.all([loadAdminProducts(), loadAdminOrders()]);
    }

    if (isOrderTrackingPage && trackingCode.value) {
        await findOrder();
    }

    if (isPcBuilderPage) {
        await loadBuilderProducts();
        await loadSavedBuilds();
        isLoadingProducts.value = false;

        return;
    }

    if (isHomePage.value || isCartPage || isWishlistPage || isCheckoutPage || isCardCheckoutPage || isCheckoutSuccessPage || isAccountOrdersPage || isAdminProductsPage || isOrderTrackingPage) {
        isLoadingProducts.value = false;

        return;
    }

    try {
        const response = await fetch(productsEndpoint);
        const data = await response.json();

        products.value = data.products ?? [];
    } finally {
        isLoadingProducts.value = false;
    }
});
</script>

<template>
    <div class="site-shell">
        <header class="store-header">
            <div class="top-bar">
                <p>{{ t('Δωρεάν μεταφορικά για παραγγελίες άνω των 50€') }}</p>
                <div class="top-bar__links">
                    <a href="tel:+302101234567">{{ t('Υποστήριξη: 210 123 4567') }}</a>
                    <Select v-model="language" :options="languageOptions" :aria-label="t('Επιλογή γλώσσας')" />
                </div>
            </div>

            <div class="main-header">
                <a class="brand" href="/" :aria-label="language === 'EN' ? 'specs-store home' : 'specs-store αρχική'" @click.prevent="goHome">
                    <span class="brand-copy">
                        <strong>specs-store</strong>
                        <small>PC hardware specialists</small>
                    </span>
                </a>

                <form class="search-box" action="/search" role="search" @submit.prevent="submitSearch">
                    <div class="search-box__bar">
                        <InputText
                            v-model="searchQuery"
                            name="q"
                            type="search"
                            :aria-label="t('Αναζήτηση')"
                            :placeholder="t('Αναζήτηση με SKU ή μοντέλο')"
                            @focus="searchQuery.trim() ? isSearchPreviewOpen = true : null"
                        />
                        <Button type="submit" :label="t('Αναζήτηση')" class="search-submit" />
                    </div>

                    <div v-if="isSearchPreviewOpen" class="search-preview" @mousedown.prevent>
                        <div class="search-preview__heading">
                            <span>{{ t('Αποτελέσματα από όλες τις κατηγορίες') }}</span>
                            <strong v-if="!isLoadingSearchPreview">{{ searchPreviewProducts.length }}</strong>
                        </div>

                        <p v-if="isLoadingSearchPreview" class="search-preview__empty">{{ t('Αναζήτηση προϊόντων...') }}</p>
                        <p v-else-if="searchPreviewProducts.length === 0" class="search-preview__empty">{{ t('Δεν βρέθηκαν προϊόντα.') }}</p>

                        <template v-else>
                            <a
                                v-for="product in searchPreviewProducts"
                                :key="`preview-${product.sku}`"
                                class="search-preview__item"
                                :href="`/search?q=${encodeURIComponent(searchQuery.trim())}`"
                            >
                                <img :src="product.image" :alt="product.name">
                                <span>
                                    <strong>{{ product.name }}</strong>
                                    <small>{{ product.category }} · {{ product.brand }}</small>
                                </span>
                                <em>{{ product.is_on_sale ? product.formatted_sale_price : product.formatted_price }}</em>
                            </a>
                        </template>
                    </div>
                </form>

                <nav class="user-actions" :aria-label="t('Ενέργειες χρήστη')">
                    <div class="user-actions__primary">
                    <Button v-if="!authUser" as="a" href="/login" :label="t('Σύνδεση')" severity="secondary" outlined>
                        <template #default>
                            <span>{{ t('Λογαριασμός') }}</span>
                            {{ t('Σύνδεση') }}
                        </template>
                    </Button>
                    <div v-else class="account-card">
                        <div class="account-card__identity">
                            <span>{{ t('Λογαριασμός') }}</span>
                            <strong>{{ authUser.name }}</strong>
                            <small>{{ authUser.email }}</small>
                        </div>
                        <div class="account-card__actions">
                            <a v-if="authUser.is_admin" class="admin-dashboard-button" href="/admin/products">
                                <i class="pi pi-cog" aria-hidden="true"></i>
                                Dashboard
                            </a>
                            <a class="orders-button" href="/account/orders">{{ language === 'EN' ? 'Orders' : 'Παραγγελίες' }}</a>
                            <button type="button" class="sign-out-button" :disabled="isSigningOut" @click="signOut">
                                {{ isSigningOut ? 'Signing out...' : 'Sign out' }}
                            </button>
                        </div>
                    </div>
                    <Button as="a" href="/wishlist" label="Wishlist" severity="secondary" outlined>
                        <template #default>
                            <span>{{ t('Αγαπημένα') }}</span>
                            {{ wishlistTitle }}
                        </template>
                    </Button>
                    <Button as="a" href="/cart" severity="secondary" outlined class="cart-preview">
                        <template #default>
                            <span>{{ t('Καλάθι') }}</span>
                            {{ cartTitle }}
                            <strong>{{ cart.formatted_total }}</strong>
                        </template>
                    </Button>
                    </div>
                    <form class="order-tracking-shortcut" action="/track-order" method="get">
                        <InputText
                            v-model="trackingCode"
                            name="code"
                            maxlength="26"
                            autocomplete="off"
                            :placeholder="t('Μοναδικός κωδικός παραγγελίας')"
                            :aria-label="t('Μοναδικός κωδικός παραγγελίας')"
                        />
                        <Button type="submit" :label="t('Αναζήτηση')" icon="pi pi-search" />
                    </form>
                </nav>
            </div>

            <nav class="main-navigation" :aria-label="t('Κύρια πλοήγηση')">
                <div class="mega-menu">
                    <Button
                        type="button"
                        :label="t('Κατηγορίες Προϊόντων')"
                        icon="pi pi-angle-down"
                        icon-pos="right"
                        :aria-expanded="String(isMainMenuOpen)"
                        aria-haspopup="true"
                        text
                        @click="toggleMainMenu"
                    />
                    <TieredMenu v-if="isMainMenuOpen" :model="mainCategoryItems" class="mega-menu__panel" />
                </div>
                <Button as="a" href="/pc-builder" label="PC Builder Tool" text />
                <Button as="a" href="/offers" :label="t('Προσφορές')" text severity="danger" />
            </nav>

            <section class="brand-strip" aria-label="Top brands">
                <div class="brand-marquee">
                    <div v-for="group in 2" :key="group" class="brand-marquee__track" :aria-hidden="group === 2">
                        <a v-for="brand in topBrands" :key="`${group}-${brand.name}`" class="brand-logo-card" href="#">
                            <img :src="brand.logo" :alt="`${brand.name} logo`">
                        </a>
                    </div>
                </div>
            </section>
        </header>

        <main :class="{ 'home-main': isHomePage }" :style="isHomePage ? homeBackgroundStyle : null">
            <div v-if="cartStockError" class="stock-notice" role="alert">
                <i class="pi pi-exclamation-triangle" aria-hidden="true"></i>
                <span>{{ cartStockError }}</span>
                <Button type="button" icon="pi pi-times" text rounded :aria-label="t('Κλείσιμο μηνύματος')" @click="cartStockError = ''" />
            </div>
            <section v-if="isHomePage" class="hero" id="pc-builder">
                <img class="hero-background" :src="heroBackgroundImage" alt="" aria-hidden="true">
                <div class="hero-copy">
                    <p class="eyebrow">Build-ready components</p>
                    <h1>{{ t('Στήσε το επόμενο PC σου με καθαρά specs') }}</h1>
                    <p>{{ t('Διάλεξε parts, έλεγξε συμβατότητα και κράτα το budget σου σε τάξη από την πρώτη οθόνη.') }}</p>
                    <Button as="a" href="/pc-builder" :label="t('Άνοιγμα PC Builder')" class="primary-button" />
                </div>
                <div class="spec-panel" :aria-label="t('Προτεινόμενη σύνθεση')">
                    <div v-for="[part, value] in buildSpecs" :key="part">
                        <span>{{ part }}</span>
                        <strong>{{ value }}</strong>
                    </div>
                </div>
            </section>

            <section v-if="isPcBuilderPage" class="pc-builder-page">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">PC Builder Tool</p>
                        <h2>{{ t('Διάλεξε συμβατά εξαρτήματα από τη βάση') }}</h2>
                    </div>
                    <div class="builder-summary">
                        <span>{{ selectedBuilderCount }}/8 {{ language === 'EN' ? 'selected' : 'επιλογές' }}</span>
                        <strong>{{ formattedBuilderTotal }}</strong>
                    </div>
                </div>

                <div class="builder-toolbar">
                    <Button
                        type="button"
                        :label="t('Καθαρισμός σύνθεσης')"
                        icon="pi pi-eraser"
                        severity="secondary"
                        outlined
                        @click="clearBuilderSelection"
                    />
                    <div v-if="authUser" class="builder-saved-controls">
                        <Select
                            v-model="selectedSavedBuild"
                            :options="savedBuilds"
                            option-label="name"
                            :placeholder="t('Φόρτωση αποθηκευμένης σύνθεσης')"
                            class="builder-load-select"
                            :loading="isLoadingSavedBuilds"
                            @update:model-value="loadSavedBuild"
                        >
                            <template #option="{ option }">
                                <div class="builder-load-option">
                                    <strong>{{ option.name }}</strong>
                                    <span>{{ option.updated_at }}</span>
                                </div>
                            </template>
                        </Select>
                        <Button type="button" icon="pi pi-trash" severity="danger" outlined :aria-label="t('Διαγραφή αποθηκευμένης σύνθεσης')" :title="t('Διαγραφή αποθηκευμένης σύνθεσης')" :disabled="!selectedSavedBuild" :loading="isDeletingSavedBuild" @click="deleteSavedBuild" />
                    </div>
                </div>

                <div v-if="isLoadingBuilderProducts" class="builder-grid" aria-live="polite">
                    <div v-for="placeholder in 8" :key="placeholder" class="product-skeleton"></div>
                </div>

                <div v-else class="builder-layout">
                    <div class="builder-grid">
                        <article
                            v-for="slot in builderSlots"
                            :key="slot.key"
                            class="builder-slot"
                            :class="{ 'builder-slot--warning': builderProductCompatibilityIssues(slot.key).length > 0 }"
                        >
                            <div class="builder-slot__header">
                                <div>
                                    <span>{{ slot.label }}</span>
                                    <h3>{{ builderSelection[slot.key]?.name ?? slot.empty }}</h3>
                                </div>
                                <button
                                    v-if="builderSelection[slot.key]"
                                    type="button"
                                    :aria-label="language === 'EN' ? 'Clear selection' : 'Καθαρισμός επιλογής'"
                                    @click="clearBuilderProduct(slot.key)"
                                >
                                    <i class="pi pi-times" aria-hidden="true"></i>
                                </button>
                            </div>

                            <Select
                                :model-value="builderSelection[slot.key]"
                                :options="builderSlotOptions(slot.key)"
                                option-label="name"
                                :placeholder="slot.empty"
                                class="builder-select"
                                filter
                                @update:model-value="selectBuilderProduct(slot.key, $event)"
                            />

                            <div v-if="builderSelection[slot.key]" class="builder-product">
                                <img :src="builderSelection[slot.key].image" :alt="builderSelection[slot.key].name">
                                <div>
                                    <span>{{ builderSelection[slot.key].brand }}</span>
                                    <strong>{{ builderSelection[slot.key].is_on_sale ? builderSelection[slot.key].formatted_sale_price : builderSelection[slot.key].formatted_price }}</strong>
                                    <p>{{ builderSelection[slot.key].sku }}</p>
                                </div>
                            </div>

                            <p v-else class="builder-slot__hint">
                                {{ builderSlotOptions(slot.key).length }} {{ t('διαθέσιμα προϊόντα') }}
                                <span v-if="compatibleBuilderProducts(slot.key).length !== builderSlotOptions(slot.key).length">
                                    / {{ compatibleBuilderProducts(slot.key).length }} {{ t('συμβατά με τις τωρινές επιλογές') }}
                                </span>
                            </p>
                            <p v-if="builderProductCompatibilityIssues(slot.key).length > 0" class="builder-slot__warning">
                                {{ builderProductCompatibilityIssues(slot.key)[0] }}
                            </p>
                        </article>
                    </div>

                    <aside class="builder-review">
                        <span>Compatibility</span>
                        <strong :class="{ 'builder-review__status--warning': builderCompatibilityMessages.length > 0 }">
                            {{ builderCompatibilityMessages.length === 0 ? t('Συμβατή σύνθεση') : t('Χρειάζεται αλλαγή') }}
                        </strong>
                        <p v-if="recommendedPsuWatts > 0">{{ language === 'EN' ? 'Recommended PSU' : 'Προτεινόμενο PSU' }}: {{ recommendedPsuWatts }}W+</p>
                        <ul v-if="builderCompatibilityMessages.length > 0">
                            <li v-for="message in builderCompatibilityMessages" :key="message">{{ message }}</li>
                        </ul>
                        <dl>
                            <div v-for="slot in builderSlots" :key="`summary-${slot.key}`">
                                <dt>{{ slot.label }}</dt>
                                <dd>{{ builderSelection[slot.key]?.name ?? '-' }}</dd>
                            </div>
                        </dl>
                        <div class="builder-review__total">
                            <span>{{ t('Σύνολο') }}</span>
                            <strong>{{ formattedBuilderTotal }}</strong>
                        </div>
                        <Button
                            type="button"
                            :label="t('Προσθήκη σύνθεσης στο καλάθι')"
                            icon="pi pi-shopping-cart"
                            :disabled="!isBuilderComplete || builderCompatibilityMessages.length > 0"
                            @click="addBuildToCart"
                        />
                        <div v-if="authUser" class="builder-save-panel">
                            <label>
                                <span>{{ t('Όνομα σύνθεσης') }}</span>
                                <InputText v-model="buildName" :placeholder="language === 'EN' ? 'e.g. AM5 gaming build' : 'π.χ. Gaming build AM5'" />
                            </label>
                            <Button
                                type="button"
                                :label="loadedSavedBuild && hasLoadedBuildChanges ? t('Αποθήκευση ως νέα σύνθεση') : t('Αποθήκευση σύνθεσης')"
                                icon="pi pi-save"
                                :loading="isSavingBuild"
                                :disabled="!isBuilderComplete || builderCompatibilityMessages.length > 0"
                                @click="saveBuild(false)"
                            />
                            <Button
                                v-if="loadedSavedBuild"
                                type="button"
                                :label="t('Αντικατάσταση υπάρχουσας')"
                                icon="pi pi-refresh"
                                severity="secondary"
                                outlined
                                :loading="isSavingBuild"
                                :disabled="!hasLoadedBuildChanges || !isBuilderComplete || builderCompatibilityMessages.length > 0"
                                @click="saveBuild(true)"
                            />
                            <p v-if="builderSaveMessage">{{ builderSaveMessage }}</p>
                        </div>
                        <p v-else class="builder-login-note">{{ t('Συνδέσου για να αποθηκεύσεις και να φορτώσεις συνθέσεις.') }}</p>
                    </aside>
                </div>
            </section>

            <section v-else-if="isCartPage" class="cart-page" id="cart">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Shopping cart</p>
                        <h2>{{ t('Το καλάθι σου') }}</h2>
                    </div>
                    <div class="cart-summary">
                        <span>{{ cartTitle }}</span>
                        <strong>{{ cart.formatted_total }}</strong>
                    </div>
                </div>

                <div v-if="cart.items.length === 0" class="empty-cart">
                    <h3>{{ t('Το καλάθι είναι άδειο') }}</h3>
                    <p>{{ t('Πρόσθεσε προϊόντα από την αρχική ή από κάποια κατηγορία.') }}</p>
                    <Button as="a" href="/" :label="t('Συνέχεια αγορών')" icon="pi pi-arrow-left" />
                </div>

                <div v-else class="cart-layout">
                    <div class="cart-items">
                        <article v-for="item in cart.items" :key="item.id" class="cart-item">
                            <img :src="item.image" :alt="item.name">
                            <div class="cart-item__details">
                                <span>{{ item.brand }}</span>
                                <h3>{{ item.name }}</h3>
                                <p>{{ item.sku }}</p>
                            </div>
                            <div class="cart-item__quantity">
                                <span>{{ t('Ποσότητα') }}</span>
                                <div class="quantity-control" :aria-label="`${t('Ποσότητα')} ${language === 'EN' ? 'for' : 'για'} ${item.name}`">
                                    <button
                                        type="button"
                                        :aria-label="t('Μείωση ποσότητας')"
                                        :disabled="item.quantity <= 1"
                                        @click="updateCartQuantity(item, item.quantity - 1)"
                                    >
                                        <i class="pi pi-minus" aria-hidden="true"></i>
                                    </button>
                                    <input
                                        type="number"
                                        min="1"
                                        max="99"
                                        :value="item.quantity"
                                        :aria-label="t('Ποσότητα')"
                                        @change="updateCartQuantity(item, $event.target.value)"
                                    >
                                    <button
                                        type="button"
                                        :aria-label="t('Αύξηση ποσότητας')"
                                        :disabled="item.quantity >= 99"
                                        @click="updateCartQuantity(item, item.quantity + 1)"
                                    >
                                        <i class="pi pi-plus" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="cart-item__price">
                                <span>{{ item.formatted_price }} / {{ t('τεμ.') }}</span>
                                <strong>{{ item.formatted_line_total }}</strong>
                            </div>
                            <Button
                                type="button"
                                :label="t('Αφαίρεση')"
                                icon="pi pi-trash"
                                severity="danger"
                                outlined
                                @click="removeFromCart(item)"
                            />
                        </article>
                    </div>

                    <aside class="cart-total-panel" :aria-label="t('Σύνοψη καλαθιού')">
                        <span>{{ t('Σύνολο') }}</span>
                        <strong>{{ cart.formatted_total }}</strong>
                        <p>{{ cartTitle }} {{ language === 'EN' ? 'in cart' : 'στο καλάθι' }}</p>
                        <Button type="button" :label="t('Ολοκλήρωση παραγγελίας')" icon="pi pi-credit-card" :loading="isValidatingCartStock" :disabled="cart.items.length === 0" @click="proceedToCheckout" />
                    </aside>
                </div>
            </section>

            <section v-else-if="isWishlistPage" class="wishlist-page" id="wishlist">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Wishlist</p>
                        <h2>{{ t('Τα αγαπημένα σου') }}</h2>
                    </div>
                    <div class="wishlist-summary">
                        <span>{{ wishlistTitle }}</span>
                        <strong>Saved picks</strong>
                    </div>
                </div>

                <div v-if="wishlist.items.length === 0" class="empty-cart">
                    <h3>{{ t('Η wishlist είναι άδεια') }}</h3>
                    <p>{{ t('Πάτησε την καρδιά σε προϊόντα που θέλεις να κρατήσεις για αργότερα.') }}</p>
                    <Button as="a" href="/" :label="t('Συνέχεια αγορών')" icon="pi pi-arrow-left" />
                </div>

                <div v-else class="wishlist-grid">
                    <article v-for="item in wishlist.items" :key="item.id" class="wishlist-item">
                        <img :src="item.image" :alt="item.name">
                        <div class="wishlist-item__details">
                            <span>{{ item.brand }}</span>
                            <h3>{{ item.name }}</h3>
                            <p>{{ item.sku }}</p>
                        </div>
                        <div class="product-card__price">
                            <strong>{{ item.is_on_sale ? item.formatted_sale_price : item.formatted_price }}</strong>
                            <del v-if="item.is_on_sale">{{ item.formatted_price }}</del>
                        </div>
                        <div class="wishlist-item__actions">
                            <Button
                                type="button"
                                :label="t('Στο καλάθι')"
                                icon="pi pi-shopping-cart"
                                @click="addToCart(item)"
                            />
                            <Button
                                type="button"
                                :label="t('Αφαίρεση')"
                                icon="pi pi-heart-fill"
                                severity="danger"
                                outlined
                                @click="removeFromWishlist(item)"
                            />
                        </div>
                    </article>
                </div>
            </section>

            <section v-else-if="isCheckoutPage" class="checkout-page">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Checkout</p>
                        <h2>{{ t('Στοιχεία παράδοσης και πληρωμής') }}</h2>
                    </div>
                    <div class="cart-summary">
                        <span>{{ cartTitle }}</span>
                        <strong>{{ cart.formatted_total }}</strong>
                    </div>
                </div>

                <div v-if="cart.items.length === 0" class="empty-cart">
                    <h3>{{ t('Δεν υπάρχουν προϊόντα για ολοκλήρωση') }}</h3>
                    <p>{{ t('Πρόσθεσε προϊόντα στο καλάθι πριν συνεχίσεις.') }}</p>
                    <Button as="a" href="/" :label="t('Συνέχεια αγορών')" icon="pi pi-arrow-left" />
                </div>

                <form v-else class="checkout-layout" @submit.prevent="submitCheckout">
                    <div class="checkout-form">
                        <p v-if="checkoutErrors.form || checkoutErrors.cart" class="form-error">{{ (checkoutErrors.form ?? checkoutErrors.cart)[0] }}</p>

                        <div class="checkout-fields">
                            <label>
                                <span>{{ t('Όνομα') }}</span>
                                <InputText v-model="checkoutForm.first_name" :invalid="Boolean(checkoutErrors.first_name)" />
                                <small v-if="checkoutErrors.first_name">{{ checkoutErrors.first_name[0] }}</small>
                            </label>
                            <label>
                                <span>{{ t('Επώνυμο') }}</span>
                                <InputText v-model="checkoutForm.last_name" :invalid="Boolean(checkoutErrors.last_name)" />
                                <small v-if="checkoutErrors.last_name">{{ checkoutErrors.last_name[0] }}</small>
                            </label>
                            <label>
                                <span>{{ t('Τηλέφωνο') }}</span>
                                <InputText v-model="checkoutForm.phone" :invalid="Boolean(checkoutErrors.phone)" />
                                <small v-if="checkoutErrors.phone">{{ checkoutErrors.phone[0] }}</small>
                            </label>
                            <label>
                                <span>Email</span>
                                <InputText v-model="checkoutForm.email" type="email" :invalid="Boolean(checkoutErrors.email)" />
                                <small v-if="checkoutErrors.email">{{ checkoutErrors.email[0] }}</small>
                            </label>
                            <label class="checkout-field--wide">
                                <span>{{ t('Διεύθυνση') }}</span>
                                <InputText v-model="checkoutForm.address" :invalid="Boolean(checkoutErrors.address)" />
                                <small v-if="checkoutErrors.address">{{ checkoutErrors.address[0] }}</small>
                            </label>
                        </div>

                        <div class="delivery-map" role="button" tabindex="0" @click="placeDeliveryPin">
                            <span class="delivery-map__road delivery-map__road--one"></span>
                            <span class="delivery-map__road delivery-map__road--two"></span>
                            <span class="delivery-map__label">{{ t('Προαιρετική τοποθεσία παράδοσης') }}</span>
                            <span
                                v-if="checkoutForm.delivery_latitude && checkoutForm.delivery_longitude"
                                class="delivery-map__pin"
                                :style="{ left: `${deliveryPin?.x ?? 50}%`, top: `${deliveryPin?.y ?? 50}%` }"
                                aria-hidden="true"
                            ></span>
                        </div>
                        <p v-if="checkoutForm.delivery_latitude && checkoutForm.delivery_longitude" class="map-coordinates">
                            Pin: {{ checkoutForm.delivery_latitude }}, {{ checkoutForm.delivery_longitude }}
                        </p>

                        <div class="payment-options">
                            <label>
                                <input v-model="checkoutForm.payment_method" type="radio" value="CASH">
                                <span>{{ t('Αντικαταβολή') }}</span>
                            </label>
                            <label>
                                <input v-model="checkoutForm.payment_method" type="radio" value="CARD">
                                <span>{{ t('Πληρωμή με κάρτα') }}</span>
                            </label>
                            <label>
                                <input v-model="checkoutForm.payment_method" type="radio" value="PAYPAL">
                                <span>PayPal</span>
                            </label>
                        </div>

                        <Button type="submit" :label="t('Συνέχεια')" icon="pi pi-arrow-right" :loading="isSubmittingCheckout" />
                    </div>

                    <aside class="checkout-summary">
                        <span>{{ t('Σύνοψη') }}</span>
                        <article v-for="item in cart.items" :key="item.id">
                            <p>{{ item.name }}</p>
                            <strong>{{ item.quantity }} x {{ item.formatted_price }}</strong>
                        </article>
                        <div>
                            <span>{{ t('Σύνολο') }}</span>
                            <strong>{{ cart.formatted_total }}</strong>
                        </div>
                    </aside>
                </form>
            </section>

            <section v-else-if="isCardCheckoutPage" class="checkout-page">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Card payment</p>
                        <h2>{{ t('Στοιχεία κάρτας') }}</h2>
                    </div>
                </div>

                <form class="card-form" @submit.prevent="submitCard">
                    <p v-if="cardErrors.form || cardErrors.cart" class="form-error">{{ (cardErrors.form ?? cardErrors.cart)[0] }}</p>
                    <label>
                        <span>{{ t('Ονοματεπώνυμο κατόχου') }}</span>
                        <InputText v-model="cardForm.cardholder_name" :invalid="Boolean(cardErrors.cardholder_name)" />
                        <small v-if="cardErrors.cardholder_name">{{ cardErrors.cardholder_name[0] }}</small>
                    </label>
                    <label>
                        <span>{{ t('Αριθμός κάρτας') }}</span>
                        <InputText v-model="cardForm.card_number" inputmode="numeric" :invalid="Boolean(cardErrors.card_number)" />
                        <small v-if="cardErrors.card_number">{{ cardErrors.card_number[0] }}</small>
                    </label>
                    <div class="card-form__row">
                        <label>
                            <span>{{ t('Μήνας') }}</span>
                            <InputText v-model="cardForm.expiry_month" inputmode="numeric" :invalid="Boolean(cardErrors.expiry_month)" />
                            <small v-if="cardErrors.expiry_month">{{ cardErrors.expiry_month[0] }}</small>
                        </label>
                        <label>
                            <span>{{ t('Έτος') }}</span>
                            <InputText v-model="cardForm.expiry_year" inputmode="numeric" :invalid="Boolean(cardErrors.expiry_year)" />
                            <small v-if="cardErrors.expiry_year">{{ cardErrors.expiry_year[0] }}</small>
                        </label>
                        <label>
                            <span>CVV</span>
                            <InputText v-model="cardForm.cvv" inputmode="numeric" :invalid="Boolean(cardErrors.cvv)" />
                            <small v-if="cardErrors.cvv">{{ cardErrors.cvv[0] }}</small>
                        </label>
                    </div>
                    <Button type="submit" :label="t('Ολοκλήρωση πληρωμής')" icon="pi pi-lock" :loading="isSubmittingCard" />
                </form>
            </section>

            <section v-else-if="isCheckoutSuccessPage" class="checkout-success">
                <i class="pi pi-check-circle" aria-hidden="true"></i>
                <p class="eyebrow">Order placed</p>
                <h2>{{ t('Η παραγγελία καταχωρήθηκε επιτυχώς') }}</h2>
                <p>{{ t('Στείλαμε email επιβεβαίωσης στη διεύθυνση που καταχώρησες.') }}</p>
                <div v-if="successTrackingCode" class="tracking-code-box">
                    <span>{{ t('Κωδικός παραγγελίας') }}</span>
                    <strong>{{ successTrackingCode }}</strong>
                </div>
                <Button as="a" :href="`/track-order?code=${encodeURIComponent(successTrackingCode)}`" :label="t('Προβολή παραγγελίας')" icon="pi pi-search" severity="secondary" outlined />
                <Button as="a" href="/" :label="t('Επιστροφή στο κατάστημα')" icon="pi pi-arrow-left" />
            </section>

            <section v-else-if="isOrderTrackingPage" class="order-tracking-page">
                <div class="section-heading"><div><p class="eyebrow">Order tracking</p><h2>{{ t('Αναζήτηση παραγγελίας') }}</h2></div></div>
                <form class="order-tracking-form" @submit.prevent="findOrder">
                    <label><span>{{ t('Μοναδικός κωδικός παραγγελίας') }}</span><InputText v-model="trackingCode" maxlength="26" autocomplete="off" /></label>
                    <Button type="submit" :label="t('Αναζήτηση')" icon="pi pi-search" :loading="isTrackingOrder" />
                    <p v-if="trackingError" class="form-error">{{ trackingError }}</p>
                </form>
                <article v-if="trackedOrder" class="tracked-order">
                    <header><div><span>{{ trackedOrder.tracking_code }}</span><h3>{{ trackedOrder.customer_name }}</h3></div><Tag :value="trackedOrder.status" :severity="trackedOrder.status === 'ACTIVE' ? 'success' : 'danger'" /></header>
                    <div class="tracked-order__meta"><span>{{ trackedOrder.created_at }}</span><strong>{{ trackedOrder.total }}</strong><Tag :value="trackedOrder.payment_status" severity="info" /></div>
                    <div class="tracked-order__items"><div v-for="item in trackedOrder.items" :key="item.product_name"><span>{{ item.product_name }}</span><strong>{{ item.quantity }} × {{ item.line_total }}</strong></div></div>
                </article>
            </section>

            <section v-else-if="isAdminProductsPage" class="admin-products-page">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Admin panel</p>
                        <h2>{{ selectedAdminView === 'products' ? t('Προϊόντα βάσης') : t('Ενεργές παραγγελίες') }}</h2>
                    </div>
                    <div class="orders-summary">
                        <span>{{ selectedAdminView === 'products' ? t('Προϊόντα προβολής') : t('Ενεργές παραγγελίες') }}</span>
                        <strong>{{ selectedAdminView === 'products' ? filteredAdminProducts.length : filteredAdminOrders.length }}</strong>
                    </div>
                </div>

                <div class="admin-view-tabs" role="tablist" :aria-label="t('Προβολή dashboard')">
                    <Button type="button" :label="t('Προϊόντα')" icon="pi pi-box" :outlined="selectedAdminView !== 'products'" role="tab" :aria-selected="selectedAdminView === 'products'" @click="selectedAdminView = 'products'" />
                    <Button type="button" :label="t('Ενεργές παραγγελίες')" icon="pi pi-receipt" :outlined="selectedAdminView !== 'orders'" role="tab" :aria-selected="selectedAdminView === 'orders'" @click="selectedAdminView = 'orders'" />
                </div>

                <template v-if="selectedAdminView === 'orders'">
                    <div v-if="isLoadingAdminOrders" class="order-skeleton"></div>
                    <div v-else-if="adminOrders.length === 0" class="empty-cart">
                        <h3>{{ t('Δεν υπάρχουν ενεργές παραγγελίες') }}</h3>
                    </div>
                    <div v-else class="admin-orders-panel">
                        <label class="admin-order-filter">
                            <span>{{ t('Φίλτρο ονοματεπωνύμου') }}</span>
                            <div>
                                <i class="pi pi-search" aria-hidden="true"></i>
                                <InputText v-model="adminOrderNameFilter" :placeholder="t('Αναζήτηση πελάτη')" />
                                <Button v-if="adminOrderNameFilter" type="button" icon="pi pi-times" text rounded :aria-label="t('Καθαρισμός φίλτρου')" :title="t('Καθαρισμός φίλτρου')" @click="adminOrderNameFilter = ''" />
                            </div>
                        </label>

                        <div v-if="filteredAdminOrders.length === 0" class="empty-cart">
                            <h3>{{ t('Δεν βρέθηκαν παραγγελίες για αυτό το ονοματεπώνυμο') }}</h3>
                        </div>

                        <div v-else class="admin-order-groups">
                        <section v-for="group in groupedAdminOrders" :key="group.customer_name" class="admin-order-group">
                            <header>
                                <i class="pi pi-user" aria-hidden="true"></i>
                                <div><span>{{ t('Πελάτης') }}</span><h3>{{ group.customer_name }}</h3></div>
                                <strong>{{ group.orders.length }} {{ language === 'EN' ? (group.orders.length === 1 ? 'order' : 'orders') : (group.orders.length === 1 ? 'παραγγελία' : 'παραγγελίες') }}</strong>
                            </header>
                            <div class="admin-order-list">
                                <article v-for="order in group.orders" :key="order.id" class="admin-order-row">
                                    <div class="admin-order-row__summary"><span>#{{ order.id }}</span><strong>{{ order.total }}</strong><time>{{ order.created_at }}</time></div>
                                    <div class="admin-order-row__contact">
                                        <span><i class="pi pi-envelope" aria-hidden="true"></i>{{ order.email }}</span>
                                        <span><i class="pi pi-phone" aria-hidden="true"></i>{{ order.phone }}</span>
                                        <span><i class="pi pi-map-marker" aria-hidden="true"></i>{{ order.address }}</span>
                                    </div>
                                    <div class="admin-order-row__payment">
                                        <Tag :value="order.payment_method" severity="info" />
                                        <Tag :value="order.payment_status" :severity="order.payment_status === 'PAID' ? 'success' : 'warn'" />
                                        <Button type="button" icon="pi pi-times" :label="t('Ακύρωση')" severity="danger" outlined :loading="cancellingAdminOrderIds.includes(order.id)" @click="cancelAdminOrder(order)" />
                                    </div>
                                    <div class="admin-order-row__items">
                                        <div v-for="item in order.items" :key="`${order.id}-${item.sku}`">
                                            <span>{{ item.product_name }}</span><small>{{ item.sku }}</small><strong>{{ item.quantity }} × {{ item.line_total }}</strong>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </section>
                        </div>
                    </div>
                </template>

                <template v-else>
                <div v-if="isLoadingAdminProducts" class="order-skeleton"></div>

                <div v-else class="admin-products-panel">
                    <div class="admin-create-tools">
                        <form class="admin-create-form" @submit.prevent="createAdminBrand">
                            <div class="admin-create-form__heading">
                                <i class="pi pi-tag" aria-hidden="true"></i>
                                <div>
                                    <span>{{ t('Νέα εγγραφή') }}</span>
                                    <h3>Brand</h3>
                                </div>
                            </div>
                            <label>
                                <span>{{ t('Όνομα brand') }}</span>
                                <InputText v-model="adminBrandForm.name" :invalid="Boolean(adminCreateErrors.brand.name)" />
                                <small v-if="adminCreateErrors.brand.name">{{ adminCreateErrors.brand.name[0] }}</small>
                            </label>
                            <label>
                                <span>{{ t('Logo path (προαιρετικό)') }}</span>
                                <InputText v-model="adminBrandForm.logo_path" placeholder="images/brands/brand.svg" :invalid="Boolean(adminCreateErrors.brand.logo_path)" />
                                <small v-if="adminCreateErrors.brand.logo_path">{{ adminCreateErrors.brand.logo_path[0] }}</small>
                            </label>
                            <Button type="submit" :label="t('Καταχώριση brand')" icon="pi pi-plus" :loading="adminCreating.brand" />
                            <p v-if="adminCreateMessages.brand" class="admin-create-message">{{ adminCreateMessages.brand }}</p>
                            <div class="admin-entity-list" :aria-label="t('Καταχωρισμένα brands')">
                                <div v-for="brand in adminBrands" :key="brand.id" class="admin-entity-list__item">
                                    <span>
                                        <strong>{{ brand.name }}</strong>
                                        <small>{{ adminBrandProductCount(brand.id) }} {{ language === 'EN' ? 'products' : 'προϊόντα' }}</small>
                                    </span>
                                    <Button
                                        type="button"
                                        icon="pi pi-trash"
                                        severity="danger"
                                        text
                                        rounded
                                        :aria-label="language === 'EN' ? `Delete brand ${brand.name}` : `Διαγραφή brand ${brand.name}`"
                                        :title="language === 'EN' ? `Delete brand ${brand.name}` : `Διαγραφή brand ${brand.name}`"
                                        :loading="deletingAdminBrandIds.includes(brand.id)"
                                        @click="deleteAdminBrand(brand)"
                                    />
                                </div>
                            </div>
                        </form>

                        <form class="admin-create-form" @submit.prevent="createAdminCategory">
                            <div class="admin-create-form__heading">
                                <i class="pi pi-folder" aria-hidden="true"></i>
                                <div>
                                    <span>{{ t('Νέα εγγραφή') }}</span>
                                    <h3>{{ t('Κατηγορία προϊόντων') }}</h3>
                                </div>
                            </div>
                            <label>
                                <span>{{ t('Όνομα κατηγορίας') }}</span>
                                <InputText v-model="adminCategoryForm.name" :invalid="Boolean(adminCreateErrors.category.name)" />
                                <small v-if="adminCreateErrors.category.name">{{ adminCreateErrors.category.name[0] }}</small>
                            </label>
                            <label>
                                <span>{{ t('Γονική κατηγορία (προαιρετικό)') }}</span>
                                <Select
                                    v-model="adminCategoryForm.parent_id"
                                    :options="adminCategories"
                                    option-label="name"
                                    option-value="id"
                                    :placeholder="t('Χωρίς γονική κατηγορία')"
                                    show-clear
                                    :invalid="Boolean(adminCreateErrors.category.parent_id)"
                                />
                                <small v-if="adminCreateErrors.category.parent_id">{{ adminCreateErrors.category.parent_id[0] }}</small>
                            </label>
                            <Button type="submit" :label="t('Καταχώριση κατηγορίας')" icon="pi pi-plus" :loading="adminCreating.category" />
                            <p v-if="adminCreateMessages.category" class="admin-create-message">{{ adminCreateMessages.category }}</p>
                            <div class="admin-entity-list" :aria-label="t('Καταχωρισμένες κατηγορίες')">
                                <div v-for="category in adminCategories" :key="category.id" class="admin-entity-list__item">
                                    <span>
                                        <strong>{{ category.name }}</strong>
                                        <small>{{ adminCategoryProductCount(category.id) }} {{ language === 'EN' ? 'products' : 'προϊόντα' }}</small>
                                    </span>
                                    <Button
                                        type="button"
                                        icon="pi pi-trash"
                                        severity="danger"
                                        text
                                        rounded
                                        :aria-label="language === 'EN' ? `Delete category ${category.name}` : `Διαγραφή κατηγορίας ${category.name}`"
                                        :title="language === 'EN' ? `Delete category ${category.name}` : `Διαγραφή κατηγορίας ${category.name}`"
                                        :loading="deletingAdminCategoryIds.includes(category.id)"
                                        @click="deleteAdminCategory(category)"
                                    />
                                </div>
                            </div>
                        </form>

                        <form class="admin-create-form admin-create-form--product" @submit.prevent="createAdminProduct">
                            <div class="admin-create-form__heading">
                                <i class="pi pi-box" aria-hidden="true"></i>
                                <div>
                                    <span>{{ t('Νέα εγγραφή') }}</span>
                                    <h3>{{ t('Προϊόν') }}</h3>
                                </div>
                            </div>
                            <div class="admin-new-product-grid">
                                <label>
                                    <span>{{ t('Όνομα προϊόντος') }}</span>
                                    <InputText v-model="adminNewProductForm.name" :invalid="Boolean(adminCreateErrors.product.name)" />
                                    <small v-if="adminCreateErrors.product.name">{{ adminCreateErrors.product.name[0] }}</small>
                                </label>
                                <label>
                                    <span>SKU</span>
                                    <InputText v-model="adminNewProductForm.sku" :invalid="Boolean(adminCreateErrors.product.sku)" />
                                    <small v-if="adminCreateErrors.product.sku">{{ adminCreateErrors.product.sku[0] }}</small>
                                </label>
                                <label>
                                    <span>Brand</span>
                                    <Select
                                        v-model="adminNewProductForm.brand_id"
                                        :options="adminBrands"
                                        option-label="name"
                                        option-value="id"
                                        :placeholder="t('Επιλογή brand')"
                                        :invalid="Boolean(adminCreateErrors.product.brand_id)"
                                    />
                                    <small v-if="adminCreateErrors.product.brand_id">{{ adminCreateErrors.product.brand_id[0] }}</small>
                                </label>
                                <label>
                                    <span>{{ t('Κατηγορία') }}</span>
                                    <Select
                                        v-model="adminNewProductForm.category_id"
                                        :options="adminCategories"
                                        option-label="name"
                                        option-value="id"
                                        :placeholder="t('Επιλογή κατηγορίας')"
                                        :invalid="Boolean(adminCreateErrors.product.category_id)"
                                    />
                                    <small v-if="adminCreateErrors.product.category_id">{{ adminCreateErrors.product.category_id[0] }}</small>
                                </label>
                                <label class="admin-new-product-grid__image">
                                    <span>Image path</span>
                                    <InputText v-model="adminNewProductForm.image_path" placeholder="images/products/category/product.webp" :invalid="Boolean(adminCreateErrors.product.image_path)" />
                                    <small v-if="adminCreateErrors.product.image_path">{{ adminCreateErrors.product.image_path[0] }}</small>
                                </label>
                                <label>
                                    <span>{{ t('Τιμή (λεπτά)') }}</span>
                                    <InputText v-model.number="adminNewProductForm.price_cents" type="number" min="0" :invalid="Boolean(adminCreateErrors.product.price_cents)" />
                                    <small v-if="adminCreateErrors.product.price_cents">{{ adminCreateErrors.product.price_cents[0] }}</small>
                                </label>
                                <label>
                                    <span>{{ t('Προσφορά (λεπτά)') }}</span>
                                    <InputText v-model.number="adminNewProductForm.sale_price_cents" type="number" min="0" :invalid="Boolean(adminCreateErrors.product.sale_price_cents)" />
                                    <small v-if="adminCreateErrors.product.sale_price_cents">{{ adminCreateErrors.product.sale_price_cents[0] }}</small>
                                </label>
                                <label>
                                    <span>Stock</span>
                                    <InputText v-model.number="adminNewProductForm.stock" type="number" min="0" :invalid="Boolean(adminCreateErrors.product.stock)" />
                                    <small v-if="adminCreateErrors.product.stock">{{ adminCreateErrors.product.stock[0] }}</small>
                                </label>
                            </div>
                            <div class="admin-new-product-specs">
                                <div class="admin-new-product-specs__heading">
                                    <span>Specs</span>
                                    <Button type="button" :label="t('Προσθήκη spec')" icon="pi pi-plus" severity="secondary" outlined size="small" @click="addNewProductSpec" />
                                </div>
                                <div v-for="specRow in adminNewProductForm.specs" :key="specRow.id" class="admin-spec-row">
                                    <InputText v-model="specRow.key" placeholder="Spec" />
                                    <InputText v-model="specRow.value" :placeholder="t('Τιμή')" />
                                    <button type="button" :aria-label="t('Αφαίρεση spec')" @click="removeNewProductSpec(specRow)">
                                        <i class="pi pi-times" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="admin-create-form__actions">
                                <Button
                                    type="submit"
                                    :label="t('Καταχώριση προϊόντος')"
                                    icon="pi pi-plus"
                                    :loading="adminCreating.product"
                                    :disabled="adminBrands.length === 0 || adminCategories.length === 0"
                                />
                                <p v-if="adminCreateMessages.product" class="admin-create-message">{{ adminCreateMessages.product }}</p>
                            </div>
                        </form>
                    </div>

                    <div class="admin-filters">
                        <label>
                            <span>{{ t('Κατηγορία προϊόντος') }}</span>
                            <Select
                                v-model="selectedAdminCategory"
                                :options="adminCategoryOptions"
                                option-label="label"
                                option-value="value"
                                class="admin-category-select"
                            />
                        </label>
                    </div>

                    <div class="admin-table-wrap">
                        <table class="admin-products-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>{{ t('Εικόνα') }}</th>
                                    <th>{{ t('Προϊόν') }}</th>
                                    <th>SKU</th>
                                    <th>Brand</th>
                                    <th>{{ t('Κατηγορία') }}</th>
                                    <th>{{ t('Τιμή') }}</th>
                                    <th>{{ t('Προσφορά') }}</th>
                                    <th>Stock</th>
                                    <th>Image path</th>
                                    <th>Specs</th>
                                    <th>{{ t('Ενέργεια') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in filteredAdminProducts" :key="product.id">
                                    <td>{{ product.id }}</td>
                                    <td><img :src="product.image" :alt="product.name"></td>
                                    <td>
                                        <InputText v-model="adminProductDrafts[product.id].name" class="admin-input admin-input--wide" />
                                    </td>
                                    <td>
                                        <InputText v-model="adminProductDrafts[product.id].sku" class="admin-input" />
                                    </td>
                                    <td>
                                        <Select
                                            v-model="adminProductDrafts[product.id].brand_id"
                                            :options="adminBrands"
                                            option-label="name"
                                            option-value="id"
                                            class="admin-input"
                                        />
                                    </td>
                                    <td>
                                        <Select
                                            v-model="adminProductDrafts[product.id].category_id"
                                            :options="adminCategories"
                                            option-label="name"
                                            option-value="id"
                                            class="admin-input"
                                        />
                                    </td>
                                    <td>
                                        <InputText v-model.number="adminProductDrafts[product.id].price_cents" type="number" min="0" class="admin-input admin-input--number" />
                                        <small>{{ product.price }}</small>
                                    </td>
                                    <td>
                                        <InputText v-model.number="adminProductDrafts[product.id].sale_price_cents" type="number" min="0" class="admin-input admin-input--number" />
                                        <small>{{ product.sale_price }}</small>
                                    </td>
                                    <td>
                                        <InputText v-model.number="adminProductDrafts[product.id].stock" type="number" min="0" class="admin-input admin-input--number" />
                                    </td>
                                    <td>
                                        <InputText v-model="adminProductDrafts[product.id].image_path" class="admin-input admin-input--wide" />
                                    </td>
                                    <td>
                                        <div class="admin-specs-editor">
                                            <div
                                                v-for="specRow in adminProductDrafts[product.id].specs"
                                                :key="specRow.id"
                                                class="admin-spec-row"
                                            >
                                                <InputText v-model="specRow.key" placeholder="Spec" class="admin-spec-key" />
                                                <InputText v-model="specRow.value" :placeholder="t('Τιμή')" class="admin-spec-value" />
                                                <button type="button" :aria-label="t('Αφαίρεση spec')" @click="removeAdminProductSpec(product, specRow)">
                                                    <i class="pi pi-times" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <Button
                                                type="button"
                                                :label="t('Προσθήκη spec')"
                                                icon="pi pi-plus"
                                                severity="secondary"
                                                outlined
                                                size="small"
                                                @click="addAdminProductSpec(product)"
                                            />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="admin-product-actions">
                                            <Button
                                                type="button"
                                                :label="t('Αποθήκευση')"
                                                icon="pi pi-save"
                                                :loading="savingAdminProductIds.includes(product.id)"
                                                :disabled="deletingAdminProductIds.includes(product.id)"
                                                @click="saveAdminProduct(product)"
                                            />
                                            <Button
                                                type="button"
                                                icon="pi pi-trash"
                                                severity="danger"
                                                outlined
                                                :aria-label="t('Διαγραφή προϊόντος')"
                                                :title="t('Διαγραφή προϊόντος')"
                                                :loading="deletingAdminProductIds.includes(product.id)"
                                                :disabled="savingAdminProductIds.includes(product.id)"
                                                @click="deleteAdminProduct(product)"
                                            />
                                        </div>
                                        <p v-if="adminProductMessages[product.id]" class="admin-save-message">{{ adminProductMessages[product.id] }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                </template>
            </section>

            <section v-else-if="isAccountOrdersPage" class="account-orders-page">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Account orders</p>
                        <h2>{{ t('Οι παραγγελίες μου') }}</h2>
                    </div>
                    <div class="orders-summary">
                        <span>{{ language === 'EN' ? (accountOrders.length === 1 ? '1 order' : `${accountOrders.length} orders`) : (accountOrders.length === 1 ? '1 παραγγελία' : `${accountOrders.length} παραγγελίες`) }}</span>
                        <strong>{{ authUser?.name }}</strong>
                    </div>
                </div>

                <div v-if="isLoadingAccountOrders" class="orders-list" aria-live="polite">
                    <div v-for="placeholder in 3" :key="placeholder" class="order-skeleton"></div>
                </div>

                <div v-else-if="accountOrders.length === 0" class="empty-cart">
                    <h3>{{ t('Δεν υπάρχουν παραγγελίες') }}</h3>
                    <p>{{ t('Οι παραγγελίες που γίνονται ενώ είσαι συνδεδεμένος θα εμφανίζονται εδώ.') }}</p>
                    <Button as="a" href="/" :label="t('Συνέχεια αγορών')" icon="pi pi-arrow-left" />
                </div>

                <div v-else class="orders-list">
                    <article v-for="order in accountOrders" :key="order.id" class="order-card">
                        <div class="order-card__header">
                            <div>
                                <span>{{ t('Παραγγελία') }} #{{ order.id }}</span>
                                <h3>{{ order.formatted_total }}</h3>
                            </div>
                            <div class="order-card__badges">
                                <span :class="{ 'order-card__status--cancelled': order.status === 'CANCELLED' }">
                                    {{ order.order_status_label }}
                                </span>
                                <span>{{ order.status_label }}</span>
                                <strong>{{ order.payment_label }}</strong>
                            </div>
                        </div>
                        <p class="order-card__date">{{ order.created_at }}</p>
                        <div class="order-card__items">
                            <div v-for="item in order.items" :key="`${order.id}-${item.sku}`">
                                <span>{{ item.product_name }}</span>
                                <strong>{{ item.quantity }} x {{ item.formatted_unit_price }}</strong>
                                <em>{{ item.formatted_line_total }}</em>
                            </div>
                        </div>
                        <div class="order-card__actions">
                            <Button
                                type="button"
                                :label="t('Ακύρωση παραγγελίας')"
                                icon="pi pi-times"
                                severity="danger"
                                outlined
                                :disabled="!order.can_cancel"
                                @click="cancelOrder(order)"
                            />
                        </div>
                    </article>
                </div>
            </section>

            <section
                v-else-if="!isHomePage"
                class="product-showcase"
                :class="{
                    'product-showcase--listing': isCategoryPage || isOffersPage || isSearchPage,
                    'product-showcase--search': isSearchPage,
                }"
                id="processors"
            >
                <div class="section-heading">
                    <p class="eyebrow">{{ productSectionEyebrow }}</p>
                    <h2>{{ productSectionTitle }}</h2>
                </div>

                <div v-if="isLoadingProducts" class="product-grid" aria-live="polite">
                    <div v-for="placeholder in 8" :key="placeholder" class="product-skeleton" />
                </div>

                <div v-else-if="filteredProducts.length === 0" class="empty-cart">
                    <h3>{{ t('Δεν βρέθηκαν προϊόντα') }}</h3>
                    <p>{{ t('Δοκίμασε διαφορετική φράση αναζήτησης.') }}</p>
                </div>

                <div v-else class="product-grid">
                    <Card
                        v-for="product in filteredProducts"
                        :key="product.sku"
                        class="product-card"
                        :class="{ 'product-card--sale': product.is_on_sale }"
                    >
                        <template #header>
                            <div class="product-card__media">
                                <img :src="product.image" :alt="product.name">
                                <span v-if="product.is_on_sale" class="sale-badge">-{{ product.discount_percent }}%</span>
                                <button
                                    type="button"
                                    class="wishlist-toggle"
                                    :class="{ 'wishlist-toggle--active': wishlist.product_ids.includes(product.id) }"
                                    :aria-label="wishlist.product_ids.includes(product.id) ? t('Αφαίρεση') : (language === 'EN' ? 'Add to wishlist' : 'Προσθήκη στη wishlist')"
                                    :aria-pressed="String(wishlist.product_ids.includes(product.id))"
                                    @click="toggleWishlist(product)"
                                >
                                    <i
                                        class="pi"
                                        :class="wishlist.product_ids.includes(product.id) ? 'pi-heart-fill' : 'pi-heart'"
                                        aria-hidden="true"
                                    ></i>
                                </button>
                            </div>
                        </template>
                        <template #title>
                            {{ product.name }}
                        </template>
                        <template #subtitle>
                            {{ product.sku }}
                        </template>
                        <template #content>
                            <div class="product-card__meta">
                                <Tag :value="product.brand" severity="info" />
                                <span>{{ product.specs.socket ?? product.specs.vram ?? product.specs.capacity ?? product.specs.screen_size ?? product.specs.layout ?? product.specs.system_type ?? product.specs.controller_type ?? product.specs.use_case ?? product.specs.form_factor ?? product.specs.drive_support ?? product.specs.format ?? product.specs.drive_type ?? product.specs.case_type ?? product.specs.wattage ?? product.specs.size }}</span>
                            </div>
                            <dl>
                                <div v-for="[label, value] in productSpecs(product)" :key="`${product.sku}-${label}`">
                                    <dt>{{ specT(label) }}</dt>
                                    <dd>{{ value }}</dd>
                                </div>
                            </dl>
                        </template>
                        <template #footer>
                            <div class="product-card__price">
                                <strong>{{ product.is_on_sale ? product.formatted_sale_price : product.formatted_price }}</strong>
                                <del v-if="product.is_on_sale">{{ product.formatted_price }}</del>
                            </div>
                            <Button :label="t('Προσθήκη')" icon="pi pi-shopping-cart" @click="addToCart(product)" />
                        </template>
                    </Card>
                </div>
            </section>
        </main>
    </div>
</template>
