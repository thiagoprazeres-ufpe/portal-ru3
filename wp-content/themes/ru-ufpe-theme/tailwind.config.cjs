module.exports = {
	content: [
		'./*.php',
		'./**/*.php',
		'./assets/src/**/*.{js,css}',
	],
	theme: {
		   extend: {
			   fontFamily: {
				   body: ['Geist Variable', 'Geist', 'ui-sans-serif', 'system-ui', 'sans-serif'],
			   },
			   maxWidth: {
				   ru: '1120px',
			   },
			   keyframes: {
				   'fade-in-up': {
					   '0%': { opacity: '0', transform: 'translateY(24px)' },
					   '100%': { opacity: '1', transform: 'translateY(0)' },
				   },
			   },
			   animation: {
				   'fade-in-up': 'fade-in-up 0.7s cubic-bezier(0.22, 1, 0.36, 1) both',
			   },
		   },
	},
	plugins: [],
};
