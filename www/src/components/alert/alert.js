import { alertStore } from './store.js';

const defaultTimeout = 15000;

export function add(message, type = 'default', timeout = defaultTimeout) {
	alertStore.set({ message, type, timeout });
}

export function success(message, timeout = defaultTimeout) {
	add(message, 'success', timeout);
}

export function danger(message, timeout = defaultTimeout) {
	add(message, 'danger', timeout);
}

export function info(message, timeout = defaultTimeout) {
	add(message, 'info', timeout);
}

export function warning(message, timeout = defaultTimeout) {
	add(message, 'warning', timeout);
}

export function primary(message, timeout = defaultTimeout) {
	add(message, 'primary', timeout);
}

export function secondary(message, timeout = defaultTimeout) {
	add(message, 'secondary', timeout);
}