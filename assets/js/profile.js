const STORAGE_KEY = 'emetian_profile';

const defaults = {
	name: 'Emetian User',
	email: 'correo@ejemplo.com',
	city: '',
	username: 'emetian',
	bio: '',
	emailAlerts: true,
	weeklyReport: true,
	darkMode: false,
	saveFavorites: true
};

function loadProfile() {
	const saved = localStorage.getItem(STORAGE_KEY);
	return saved ? { ...defaults, ...JSON.parse(saved) } : { ...defaults };
}

function saveProfile(profile) {
	localStorage.setItem(STORAGE_KEY, JSON.stringify(profile));
}

function populateForm(profile) {
	document.getElementById('name').value = profile.name || '';
	document.getElementById('email').value = profile.email || '';
	document.getElementById('city').value = profile.city || '';
	document.getElementById('username').value = profile.username || '';
	document.getElementById('bio').value = profile.bio || '';
	document.getElementById('emailAlerts').checked = Boolean(profile.emailAlerts);
	document.getElementById('weeklyReport').checked = Boolean(profile.weeklyReport);
	document.getElementById('darkMode').checked = Boolean(profile.darkMode);
	document.getElementById('saveFavorites').checked = Boolean(profile.saveFavorites);
}

function updatePreview(profile) {
	const name = profile.name || defaults.name;
	const email = profile.email || defaults.email;
	document.getElementById('profileNamePreview').textContent = name;
	document.getElementById('profileEmailPreview').textContent = email;
	document.getElementById('profileAvatar').textContent = name.charAt(0).toUpperCase();
}

document.addEventListener('DOMContentLoaded', () => {
	const profile = loadProfile();
	populateForm(profile);
	updatePreview(profile);

	document.getElementById('profileForm').addEventListener('submit', (event) => {
		event.preventDefault();
		const updatedProfile = {
			name: document.getElementById('name').value.trim(),
			email: document.getElementById('email').value.trim(),
			city: document.getElementById('city').value.trim(),
			username: document.getElementById('username').value.trim(),
			bio: document.getElementById('bio').value.trim(),
			emailAlerts: document.getElementById('emailAlerts').checked,
			weeklyReport: document.getElementById('weeklyReport').checked,
			darkMode: document.getElementById('darkMode').checked,
			saveFavorites: document.getElementById('saveFavorites').checked
		};

		saveProfile(updatedProfile);
		updatePreview(updatedProfile);
		alert('Perfil guardado localmente.');
	});

	document.getElementById('resetProfile').addEventListener('click', () => {
		localStorage.removeItem(STORAGE_KEY);
		const resetProfile = { ...defaults };
		populateForm(resetProfile);
		updatePreview(resetProfile);
	});
});
