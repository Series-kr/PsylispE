class ApiClient {
    constructor() {
        this.baseUrl = '/api';
        this.csrfToken = this.getCsrfToken();
    }

    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': this.csrfToken
            },
            credentials: 'same-origin',
            ...options
        };

        if (config.body && typeof config.body === 'object') {
            config.body = JSON.stringify(config.body);
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.errors ? data.errors.join(', ') : 'Request failed');
            }

            return data;
        } catch (error) {
            this.showToast(error.message, 'error');
            throw error;
        }
    }

    async get(endpoint) {
        return this.request(endpoint);
    }

    async post(endpoint, data) {
        return this.request(endpoint, {
            method: 'POST',
            body: data
        });
    }

    async put(endpoint, data) {
        return this.request(endpoint, {
            method: 'PUT',
            body: data
        });
    }

    async delete(endpoint) {
        return this.request(endpoint, {
            method: 'DELETE'
        });
    }

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    // File upload with progress
    async uploadFile(endpoint, file, onProgress = null) {
        const formData = new FormData();
        formData.append('file', file);

        const xhr = new XMLHttpRequest();
        
        return new Promise((resolve, reject) => {
            xhr.upload.addEventListener('progress', (e) => {
                if (onProgress && e.lengthComputable) {
                    onProgress((e.loaded / e.total) * 100);
                }
            });

            xhr.addEventListener('load', () => {
                if (xhr.status === 200) {
                    resolve(JSON.parse(xhr.responseText));
                } else {
                    reject(new Error('Upload failed'));
                }
            });

            xhr.addEventListener('error', () => reject(new Error('Upload failed')));
            
            xhr.open('POST', `${this.baseUrl}${endpoint}`);
            xhr.setRequestHeader('X-CSRF-Token', this.csrfToken);
            xhr.send(formData);
        });
    }
}

const api = new ApiClient();