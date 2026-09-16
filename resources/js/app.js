import './bootstrap';

const TOKEN_KEY = 'admin_sanctum_token';
const USER_KEY = 'admin_profile';

const api = window.axios.create({
    baseURL: '/api/v1/admin',
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem(TOKEN_KEY);

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if ((error.response?.status === 401 || error.response?.status === 403) && window.location.pathname !== '/admin/login') {
            clearAuth();
            window.location.href = '/admin/login';
        }

        return Promise.reject(error);
    },
);

function clearAuth() {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
}

function setAuth(payload) {
    localStorage.setItem(TOKEN_KEY, payload.token);

    if (payload.user) {
        localStorage.setItem(USER_KEY, JSON.stringify(payload.user));
    }
}

function getAuthUser() {
    try {
        return JSON.parse(localStorage.getItem(USER_KEY) || 'null');
    } catch {
        return null;
    }
}

function collection(data) {
    return Array.isArray(data) ? data : (data?.data || []);
}

function recordId(record) {
    return record.id || record._id || record.uuid;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function initials(name = 'Admin') {
    return String(name || 'Admin').split(' ').map((part) => part[0]).join('').slice(0, 2).toUpperCase();
}

function formatDate(value) {
    if (!value) return 'Recently';
    const date = new Date(value);
    return Number.isNaN(date.valueOf()) ? 'Recently' : date.toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}

function showError(element, message) {
    element.textContent = message;
    element.classList.remove('hidden');
}

function openEditor(resource, record = {}) {
    const modal = document.querySelector('#editor-modal');
    const form = document.querySelector('#editor-form');
    const isUser = resource === 'users';
    const id = recordId(record);

    form.reset();
    form.dataset.resource = resource;
    form.dataset.id = id || '';
    document.querySelector('#editor-title').textContent = id ? `Edit ${isUser ? 'user' : 'course'}` : `Create ${isUser ? 'user' : 'course'}`;
    document.querySelector('#editor-user-fields').classList.toggle('hidden', !isUser);
    document.querySelector('#editor-course-fields').classList.toggle('hidden', isUser);
    document.querySelector('#editor-password-fields').classList.toggle('hidden', !isUser);
    document.querySelector('#editor-name').value = record.name || '';
    document.querySelector('#editor-email').value = record.email || '';
    document.querySelector('#editor-role').value = record.role || 'student';
    document.querySelector('#editor-status').value = record.status || 'active';
    document.querySelector('#editor-title-field').value = record.title || '';
    document.querySelector('#editor-description').value = record.description || '';
    document.querySelector('#editor-category').value = record.category_id || '';
    document.querySelector('#editor-password').required = !id && isUser;
    document.querySelector('#editor-error').classList.add('hidden');
    modal.classList.remove('hidden');
    document.querySelector(isUser ? '#editor-name' : '#editor-title-field').focus();
}

function closeEditor() {
    document.querySelector('#editor-modal')?.classList.add('hidden');
}

async function saveEditor(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const resource = form.dataset.resource;
    const id = form.dataset.id;
    const isUser = resource === 'users';
    const payload = isUser ? {
        name: form.querySelector('#editor-name').value.trim(),
        email: form.querySelector('#editor-email').value.trim(),
        role: form.querySelector('#editor-role').value,
        status: form.querySelector('#editor-status').value,
    } : {
        title: form.querySelector('#editor-title-field').value.trim(),
        description: form.querySelector('#editor-description').value.trim(),
        category_id: form.querySelector('#editor-category').value.trim() || null,
    };
    const password = form.querySelector('#editor-password').value;
    if (password) {
        payload.password = password;
        payload.password_confirmation = form.querySelector('#editor-password-confirmation').value;
    }

    const button = form.querySelector('[data-editor-submit]');
    const error = document.querySelector('#editor-error');
    button.disabled = true;
    error.classList.add('hidden');

    try {
        await api({ method: id ? 'put' : 'post', url: `/${resource}${id ? `/${encodeURIComponent(id)}` : ''}`, data: payload });
        closeEditor();
        await loadDashboard();
    } catch (requestError) {
        const validationMessage = Object.values(requestError.response?.data?.errors || {})[0]?.[0];
        showError(error, validationMessage || requestError.response?.data?.message || 'The record could not be saved.');
    } finally {
        button.disabled = false;
    }
}

function setLoading(button, loading) {
    button.disabled = loading;
    button.querySelector('[data-submit-label]').textContent = loading ? 'Signing in...' : 'Sign in to control room';
    button.querySelector('[data-submit-spinner]').classList.toggle('hidden', !loading);
}

function bootLogin() {
    const form = document.querySelector('#login-form');
    if (!form) return;

    if (localStorage.getItem(TOKEN_KEY)) {
        window.location.replace('/admin/dashboard');
        return;
    }

    const error = document.querySelector('#login-error');
    const submit = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        error.classList.add('hidden');
        setLoading(submit, true);

        try {
            const response = await api.post('/login', {
                email: form.email.value.trim(),
                password: form.password.value,
                device_name: 'super-admin-portal',
            });

            setAuth(response.data);
            window.location.assign('/admin/dashboard');
        } catch (requestError) {
            const validationMessage = Object.values(requestError.response?.data?.errors || {})[0]?.[0];
            showError(error, validationMessage || requestError.response?.data?.message || 'Unable to sign in. Check your credentials and try again.');
        } finally {
            setLoading(submit, false);
        }
    });
}

function setUserChrome(user) {
    const name = user?.name || 'Admin';
    document.querySelectorAll('[data-user-name]').forEach((element) => { element.textContent = name; });
    document.querySelectorAll('[data-user-email]').forEach((element) => { element.textContent = user?.email || ''; });
    document.querySelectorAll('[data-user-initials]').forEach((element) => { element.textContent = initials(name); });
}

function renderUsers(users) {
    const body = document.querySelector('#users-table-body');
    if (!users.length) {
        body.innerHTML = '<tr><td colspan="4" class="px-5 py-10 text-center text-sm text-slate-400">No users found yet.</td></tr>';
        return;
    }

    body.innerHTML = users.slice(0, 5).map((user) => {
        const name = escapeHtml(user.name || 'Unnamed user');
        const email = escapeHtml(user.email || 'No email');
        const id = escapeHtml(recordId(user));
        return `<tr class="border-t border-slate-100">
            <td class="px-5 py-4"><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-50 text-xs font-extrabold text-coral">${initials(user.name)}</span><div><p class="font-bold text-ink">${name}</p><p class="mt-0.5 text-xs text-slate-400">${email}</p></div></div></td>
            <td class="px-5 py-4 text-sm text-slate-500">${formatDate(user.created_at)}</td>
            <td class="px-5 py-4"><span class="inline-flex items-center gap-1.5 rounded-full ${user.status === 'disabled' ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'} px-2.5 py-1 text-[11px] font-bold"><span class="h-1.5 w-1.5 rounded-full ${user.status === 'disabled' ? 'bg-rose-500' : 'bg-emerald-500'}"></span>${user.status === 'disabled' ? 'Disabled' : 'Active'}</span></td>
            <td class="px-5 py-4 text-right"><button class="table-action edit" data-action="edit" data-resource="users" data-id="${id}" data-name="${name}" data-email="${email}" data-role="${escapeHtml(user.role || 'student')}" data-status="${escapeHtml(user.status || 'active')}">Edit</button><button class="table-action delete" data-resource="users" data-id="${id}" data-action="delete">Delete</button></td>
        </tr>`;
    }).join('');
}

function renderCourses(courses) {
    const body = document.querySelector('#courses-table-body');
    if (!courses.length) {
        body.innerHTML = '<tr><td colspan="4" class="px-5 py-10 text-center text-sm text-slate-400">No courses found yet.</td></tr>';
        return;
    }

    body.innerHTML = courses.slice(0, 5).map((course) => {
        const title = escapeHtml(course.title || 'Untitled course');
        const description = escapeHtml(course.description || 'No description provided');
        const id = escapeHtml(recordId(course));
        return `<tr class="border-t border-slate-100">
            <td class="px-5 py-4"><p class="font-bold text-ink">${title}</p><p class="mt-0.5 max-w-[280px] truncate text-xs text-slate-400">${description}</p></td>
            <td class="px-5 py-4 text-sm text-slate-500">${escapeHtml(course.category_id || 'Uncategorized')}</td>
            <td class="px-5 py-4"><span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-600"><span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>Published</span></td>
            <td class="px-5 py-4 text-right"><button class="table-action edit" data-action="edit" data-resource="courses" data-id="${id}" data-title="${title}" data-description="${description}" data-category="${escapeHtml(course.category_id || '')}">Edit</button><button class="table-action delete" data-resource="courses" data-id="${id}" data-action="delete">Delete</button></td>
        </tr>`;
    }).join('');
}

function setMetric(id, value) {
    const element = document.querySelector(`[data-metric="${id}"]`);
    if (element) element.textContent = value;
}

async function removeRecord(resource, id, button) {
    if (!id || !window.confirm(`Delete this ${resource.slice(0, -1)}? This cannot be undone.`)) return;
    button.disabled = true;

    try {
        await api.delete(`/${resource}/${encodeURIComponent(id)}`);
        button.closest('tr').remove();
        const metric = document.querySelector(`[data-metric="${resource}"]`);
        if (metric) metric.textContent = Math.max(0, Number(metric.textContent) - 1);
    } catch (requestError) {
        window.alert(requestError.response?.data?.message || 'The record could not be deleted.');
        button.disabled = false;
    }
}

function bootDashboard() {
    if (!document.querySelector('#dashboard-app')) return;

    if (!localStorage.getItem(TOKEN_KEY)) {
        window.location.replace('/admin/login');
        return;
    }

    setUserChrome(getAuthUser());
    const currentSection = document.body.dataset.adminPage || 'overview';
    document.querySelectorAll('[data-section]').forEach((link) => {
        link.classList.toggle('active', link.dataset.section === currentSection);
    });
    document.querySelector('#mobile-menu-button').addEventListener('click', () => {
        document.querySelector('#sidebar').classList.toggle('-translate-x-full');
        document.querySelector('#sidebar-overlay').classList.toggle('hidden');
    });
    document.querySelector('#sidebar-overlay').addEventListener('click', () => {
        document.querySelector('#sidebar').classList.add('-translate-x-full');
        document.querySelector('#sidebar-overlay').classList.add('hidden');
    });
    document.querySelector('#logout-button').addEventListener('click', async () => {
        try { await api.post('/logout'); } catch { /* The local session still needs clearing. */ }
        clearAuth();
        window.location.assign('/admin/login');
    });
    document.querySelector('#refresh-button').addEventListener('click', loadDashboard);
    document.querySelector('#new-content-button').addEventListener('click', () => openEditor('courses'));
    document.querySelector('#new-user-button').addEventListener('click', () => openEditor('users'));
    document.querySelector('#editor-form').addEventListener('submit', saveEditor);
    document.querySelector('#editor-close').addEventListener('click', closeEditor);
    document.querySelector('#editor-cancel').addEventListener('click', closeEditor);
    document.querySelector('#editor-modal').addEventListener('click', (event) => {
        if (event.target.id === 'editor-modal') closeEditor();
    });
    document.querySelector('#dashboard-content').addEventListener('click', (event) => {
        const action = event.target.closest('[data-action]');
        if (!action) return;
        if (action.dataset.action === 'delete') removeRecord(action.dataset.resource, action.dataset.id, action);
        if (action.dataset.action === 'edit') {
            const record = action.dataset.resource === 'users'
                ? { id: action.dataset.id, name: action.dataset.name, email: action.dataset.email, role: action.dataset.role, status: action.dataset.status }
                : { id: action.dataset.id, title: action.dataset.title, description: action.dataset.description, category_id: action.dataset.category };
            openEditor(action.dataset.resource, record);
        }
    });

    document.querySelectorAll('[data-section]').forEach((link) => {
        link.addEventListener('click', () => {
            document.querySelectorAll('[data-section]').forEach((item) => item.classList.remove('active'));
            link.classList.add('active');
            if (window.innerWidth < 1024) document.querySelector('#sidebar-overlay').click();
        });
    });

    loadDashboard();
}

async function loadDashboard() {
    const button = document.querySelector('#refresh-button');
    const page = document.body.dataset.adminPage || 'overview';
    const userResource = ['teachers', 'students'].includes(page) ? page : 'users';
    button?.classList.add('animate-spin');

    try {
        const [profile, summary, users, courses, lessons, categories] = await Promise.all([
            api.get('/profile'),
            api.get('/dashboard'),
            api.get(`/${userResource}`),
            api.get('/courses'),
            api.get('/lessons'),
            api.get('/categories'),
        ]);
        const userList = collection(users.data);
        const courseList = collection(courses.data);
        const lessonList = collection(lessons.data);
        const categoryList = collection(categories.data);
        const metrics = summary.data.metrics || {};
        const system = summary.data.system || {};
        setUserChrome(profile.data);
        localStorage.setItem(USER_KEY, JSON.stringify(profile.data));
        setMetric('users', metrics.users ?? userList.length);
        setMetric('courses', metrics.courses ?? courseList.length);
        setMetric('lessons', metrics.lessons ?? lessonList.length);
        setMetric('categories', metrics.categories ?? categoryList.length);
        document.querySelectorAll('[data-system]').forEach((element) => {
            const value = system[element.dataset.system] ?? 'unknown';
            element.textContent = value === 'healthy' ? 'Healthy' : value;
            element.classList.toggle('text-emerald-600', value === 'healthy');
            element.classList.toggle('text-rose-600', value === 'unavailable');
        });
        renderUsers(userList);
        renderCourses(courseList);
        document.querySelector('#last-updated').textContent = `Updated ${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
    } catch (requestError) {
        if (requestError.response?.status !== 401) {
            document.querySelector('#dashboard-error').classList.remove('hidden');
        }
    } finally {
        button?.classList.remove('animate-spin');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    bootLogin();
    bootDashboard();
});
