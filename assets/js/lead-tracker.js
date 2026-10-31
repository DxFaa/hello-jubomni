(function () {
	'use strict';

	const CAMPAIGN_TIMEOUT = 30 * 24 * 60 * 60 * 1000;

	// Safe localStorage helpers (prevents crashes in Private / Incognito mode & prototype tampering)
	const safeStorage = {
		get: function (key) {
			try {
				return localStorage.getItem(key);
			} catch (e) {
				return null;
			}
		},
		set: function (key, value) {
			try {
				localStorage.setItem(key, value);
			} catch (e) {}
		},
		remove: function (key) {
			try {
				localStorage.removeItem(key);
			} catch (e) {}
		}
	};

	const aiDomains = [
		'chatgpt.com', 'chat.openai.com', 'perplexity.ai',
		'claude.ai', 'gemini.google.com', 'copilot.com',
		'meta.ai', 'mistral.ai', 'chat.qwen.ai', 'chat.z.ai'
	];

	const cssContentMappings = {
		'btn-header': 'header',
		'btn-sticky': 'sticky',
		'btn-popup': 'popup',
		'btn-floating': 'floating',
		'btn-sidebanner': 'sidebanner',
		'btn-ctabottom': 'ctabottom',
		'btn-highlight': 'highlight',
		'cmpgn-header': 'merdeka26header',
		'cmpgn-floating': 'merdeka26floating',
		'cmpgn-ctabottom': 'merdeka26ctabottom',
		'cmpgn-popupgeneral': 'merdeka26popupgeneral',
		'cmpgn-popupharga': 'merdeka26popupharga',
		'cmpgn-popupdaftar': 'merdeka26popupdaftar',
		'cmpgn-popupstore': 'merdeka26popupstore',
		'cmpgn-popupchat': 'merdeka26popupchat',
		'cmpgn-popupanalytics': 'merdeka26popupanalytics',
		'cmpgn-popuppos': 'merdeka26popuppos',
		'cmpgn-popupkatalog': 'merdeka26popupkatalog',
		'cmpgn-popuppersediaan': 'merdeka26popuppersediaan',
		'cmpgn-popupgudang': 'merdeka26popupgudang',
		'cmpgn-popuppesanan': 'merdeka26popuppesanan',
		'cmpgn-popupkeuangan': 'merdeka26popupkeuangan'
	};

	const cf7Inputs = {
		'utm_source': 'lead_source',
		'utm_medium': 'lead_medium',
		'utm_campaign': 'lead_campaign',
		'utm_content': 'lead_content',
		'utm_term': 'lead_term',
		'ref_id': 'lead_ref_id',
		'ref_url': 'lead_ref_url',
		'first_url': 'lead_first_url'
	};

	// Prevents open redirect & phishing attacks via startsWith bypasses
	function isSafeJubelioAuthLink(anchor) {
		if (!anchor || anchor.tagName.toLowerCase() !== 'a' || !anchor.href) {
			return false;
		}
		try {
			const urlObj = new URL(anchor.href, window.location.origin);
			if (urlObj.protocol !== 'https:' || urlObj.hostname !== 'v2.jubelio.com') {
				return false;
			}
			const path = urlObj.pathname.toLowerCase();
			return path === '/auth/register' || path === '/auth/register/' ||
			       path === '/auth/login' || path === '/auth/login/';
		} catch (e) {
			return false;
		}
	}

	function isSafeWhatsAppLink(anchor) {
		if (!anchor || anchor.tagName.toLowerCase() !== 'a' || !anchor.href) {
			return false;
		}
		try {
			const urlObj = new URL(anchor.href, window.location.origin);
			if (urlObj.protocol !== 'https:' && urlObj.protocol !== 'http:') {
				return false;
			}
			if (urlObj.hostname === 'api.whatsapp.com' && urlObj.pathname.startsWith('/send')) {
				return true;
			}
			if (urlObj.hostname === 'wa.me' || urlObj.hostname.endsWith('.wa.me')) {
				return true;
			}
			return false;
		} catch (e) {
			return false;
		}
	}

	function sanitizeUrl(urlStr) {
		if (!urlStr || typeof urlStr !== 'string') return '';
		const trimmed = urlStr.trim();
		const lower = trimmed.toLowerCase();
		if (lower.startsWith('javascript:') || lower.startsWith('data:') || lower.startsWith('vbscript:')) {
			return '';
		}
		return trimmed;
	}

	function getPageAssetType() {
		const path = window.location.pathname;
		const classes = document.body ? document.body.className : '';

		const pricingPaths = ['/harga', '/harga/', '/pricing', '/pricing/', '/en/pricing', '/en/pricing/'];
		if (pricingPaths.includes(path)) return 'lpharga';

		const legacySlpPaths = [
			'/aplikasi-integrasi-marketplace/', '/aplikasi-inventory-stok-barang/',
			'/aplikasi-manajemen-order/', '/aplikasi-pembukuan/', '/aplikasi-pos/',
			'/aplikasi-stock-opname/', '/software-wms/', '/solusi-masalah-overstok-barang/',
			'/overselling/', '/multigudang/', '/aplikasi-akuntansi/',
			'/software-akuntansi-online-terbaik/', '/software-retail/',
			'/aplikasi-omnichannel-marketplace/', '/aplikasi-stok-barang/',
			'/dead-stock/', '/slow-moving-stock/'
		];

		if (classes.includes('single-slp') || legacySlpPaths.some(p => path.includes(p))) return 'slp';
		if (classes.includes('single-solusi')) return 'hp';
		if (classes.includes('single-whitepaper')) return 'whitepaper';
		if (classes.includes('single-ebook')) return 'template';
		if (classes.includes('single-post') || classes.includes('blog')) return 'blog';

		return 'lp';
	}

	function detectTrafficOrigin() {
		const ref = document.referrer ? document.referrer.toLowerCase() : '';
		if (!ref) return 'direct';
		if (ref.includes(window.location.hostname)) return null;

		const matchedAI = aiDomains.find(domain => ref.includes(domain));
		if (matchedAI) return matchedAI;

		const isSearchEngine = ref.includes('bing.') || ref.includes('yahoo.') ||
			(ref.includes('google.') && !ref.includes('mail.google.') && !ref.includes('docs.google.'));

		if (isSearchEngine) return 'organic search';
		return 'direct';
	}

	function syncToBitrix() {
		const b24Source = safeStorage.get('lead_source');
		if (!b24Source) return;

		const b24Medium = safeStorage.get('lead_medium') || '';
		const b24Campaign = safeStorage.get('lead_campaign') || '';
		const b24Content = safeStorage.get('lead_content') || '';
		const b24Term = safeStorage.get('lead_term') || '';

		const b24UtmList = {};
		if (b24Source) b24UtmList['utm_source'] = b24Source;
		if (b24Medium) b24UtmList['utm_medium'] = b24Medium;
		if (b24Campaign) b24UtmList['utm_campaign'] = b24Campaign;
		if (b24Content) b24UtmList['utm_content'] = b24Content;
		if (b24Term) b24UtmList['utm_term'] = b24Term;

		const b24UtmData = {
			ts: Math.floor(Date.now() / 1000),
			list: b24UtmList,
			gclid: null
		};

		safeStorage.set('b24_crm_guest_utm', JSON.stringify(b24UtmData));
	}

	function populateFormInputs() {
		for (const [inputName, lsKey] of Object.entries(cf7Inputs)) {
			const inputs = document.querySelectorAll(`input[name="${inputName}"]`);
			if (!inputs.length) continue;

			const lsValue = safeStorage.get(lsKey);
			if (lsValue) {
				const sanitizedValue = (inputName === 'ref_url' || inputName === 'first_url')
					? sanitizeUrl(lsValue)
					: lsValue.replace(/[\u0000-\u001F\u007F-\u009F]/g, '').trim();

				if (!sanitizedValue) continue;

				inputs.forEach(input => {
					const val = (input.value || '').trim();
					// Overwrite empty, legacy placeholder, or corrupted DirectorUnknown strings
					if (!val || val === 'Direct or Unknown' || val === 'http://DirectorUnknown' || val.indexOf('DirectorUnknown') !== -1) {
						input.value = sanitizedValue;
					}
				});
			}
		}
	}

	function initAttribution() {
		const now = Date.now();
		const lastActive = safeStorage.get('lead_last_active');

		if (lastActive && (now - parseInt(lastActive, 10)) > CAMPAIGN_TIMEOUT) {
			safeStorage.remove('lead_source');
			safeStorage.remove('lead_medium');
			safeStorage.remove('lead_campaign');
			safeStorage.remove('lead_content');
			safeStorage.remove('lead_term');
			safeStorage.remove('lead_ref_id');
			safeStorage.remove('lead_ref_url');
			safeStorage.remove('lead_first_url');
		}
		safeStorage.set('lead_last_active', now);

		const urlParams = new URLSearchParams(window.location.search);
		const currentSource = urlParams.get('utm_source');
		const currentMedium = urlParams.get('utm_medium');
		const currentCampaign = urlParams.get('utm_campaign');
		const currentTerm = urlParams.get('utm_term');

		const savedSource = safeStorage.get('lead_source');
		const currentOrigin = detectTrafficOrigin();

		const isExplicitAI = currentSource && aiDomains.some(domain => currentSource.toLowerCase().includes(domain));
		const isIncomingPaid = (currentSource || currentMedium || currentCampaign || currentTerm) && !isExplicitAI;
		const isIncomingOrganicOrAI = (currentOrigin && currentOrigin !== 'direct' && currentOrigin !== null) || isExplicitAI;

		const organicMediums = ['website', 'ai referral', 'organic search', 'referral', 'direct', 'social'];
		const savedMedium = safeStorage.get('lead_medium') || '';
		const isSavedPaid = savedSource && (safeStorage.get('lead_campaign') || !organicMediums.includes(savedMedium.toLowerCase()));

		let shouldUpdateData = false;

		if (!savedSource) {
			shouldUpdateData = true;
		} else if (isIncomingPaid) {
			shouldUpdateData = true;
		} else if (isIncomingOrganicOrAI && !isSavedPaid) {
			shouldUpdateData = true;
		}

		if (shouldUpdateData) {
			if (isIncomingPaid) {
				safeStorage.set('lead_source', currentSource || 'unknown_paid');
				safeStorage.set('lead_medium', currentMedium || 'ads');
				if (currentCampaign) safeStorage.set('lead_campaign', currentCampaign); else safeStorage.remove('lead_campaign');
				if (currentTerm) safeStorage.set('lead_term', currentTerm); else safeStorage.remove('lead_term');
			} else if (isExplicitAI) {
				safeStorage.set('lead_source', currentSource);
				safeStorage.set('lead_medium', currentMedium || 'ai referral');
				safeStorage.remove('lead_campaign');
				safeStorage.remove('lead_term');
			} else if (currentOrigin && currentOrigin !== 'direct') {
				safeStorage.set('lead_source', currentOrigin);
				safeStorage.set('lead_medium', aiDomains.some(d => currentOrigin.includes(d)) ? 'ai referral' : 'website');
				safeStorage.remove('lead_campaign');
				safeStorage.remove('lead_term');
			} else if (!savedSource) {
				safeStorage.set('lead_source', 'direct');
				safeStorage.set('lead_medium', 'website');
				safeStorage.remove('lead_campaign');
				safeStorage.remove('lead_term');
			}

			const currentLandingPage = window.location.origin + window.location.pathname;
			safeStorage.set('lead_first_url', currentLandingPage);
			safeStorage.set('lead_content', getPageAssetType());
		}

		const isFormDestination = window.location.pathname.includes('/daftar-jubelio') ||
			window.location.pathname.includes('/request-demo') ||
			window.location.pathname.includes('/register-page') ||
			window.location.pathname.includes('/update-page-new');

		const existingRefId = safeStorage.get('lead_ref_id');

		if (!isFormDestination || !existingRefId) {
			let refId = '';
			if (document.body && document.body.classList) {
				document.body.classList.forEach(cls => {
					if (cls.startsWith('postid-') || cls.startsWith('page-id-')) {
						refId = cls.replace('postid-', '').replace('page-id-', '');
					}
				});
			}

			if (refId) {
				safeStorage.set('lead_ref_id', refId);
			}

			if (!isFormDestination) {
				safeStorage.set('lead_ref_url', window.location.origin + window.location.pathname);
			}
		}

		syncToBitrix();

		['mouseover', 'mousedown', 'touchstart', 'click', 'auxclick'].forEach(eventType => {
			document.body.addEventListener(eventType, function (e) {
				const interactable = e.target.closest('a, button');
				if (!interactable) return;

				if (eventType !== 'mouseover') {
					safeStorage.set('lead_last_active', Date.now());
				}

				let matchedAction = null;
				for (const cssClass in cssContentMappings) {
					if (interactable.classList.contains(cssClass) || interactable.closest(`.${cssClass}`)) {
						matchedAction = cssContentMappings[cssClass];
						break;
					}
				}

				let existingContent = safeStorage.get('lead_content') || '';
				let baseAsset = existingContent.includes('+') ? existingContent.split('+')[0] : existingContent;

				if (matchedAction) {
					safeStorage.set('lead_content', baseAsset !== matchedAction ? `${baseAsset}+${matchedAction}` : matchedAction);
				} else if (baseAsset) {
					safeStorage.set('lead_content', baseAsset);
				}

				syncToBitrix();

				if (eventType === 'click' || eventType === 'auxclick' || (eventType === 'mousedown' && e.button === 2)) {
					if (isSafeWhatsAppLink(interactable)) {
						const sendBitrixTrace = function () {
							try {
								if (window.BX && window.BX.SiteButton && window.b24Tracker && window.b24Tracker.guest) {
									let trace = window.BX.SiteButton.getTrace({
										channels: [{ "code": "imol", "value": "askarasoft_conn_wa_core" }]
									});
									if (trace) window.b24Tracker.guest.storeTrace(trace);
								}
							} catch (err) {}
						};

						if (eventType === 'click' || (eventType === 'auxclick' && e.button === 1)) {
							e.preventDefault();

							const isLottie = interactable.querySelector('.e-lottie__animation');
							const originalContent = interactable.innerHTML;
							const originalOpacity = interactable.style.opacity || '1';

							if (!isLottie) {
								interactable.textContent = 'Mohon tunggu...';
							}

							interactable.style.opacity = '0.7';
							interactable.style.pointerEvents = 'none';

							sendBitrixTrace();

							setTimeout(() => {
								if (!isLottie) {
									interactable.innerHTML = originalContent;
								}
								interactable.style.opacity = originalOpacity;
								interactable.style.pointerEvents = 'auto';
								window.open(interactable.href, '_blank', 'noopener,noreferrer');
							}, 350);

							return;
						} else if (eventType === 'mousedown' && e.button === 2) {
							sendBitrixTrace();
							return;
						}
					}
				}

				if (isSafeJubelioAuthLink(interactable)) {
					try {
						const urlObj = new URL(interactable.href);

						const lsSource   = safeStorage.get('lead_source');
						const lsMedium   = safeStorage.get('lead_medium');
						const lsCampaign = safeStorage.get('lead_campaign');
						const lsContent  = safeStorage.get('lead_content');
						const lsTerm     = safeStorage.get('lead_term');
						const lsRefId    = safeStorage.get('lead_ref_id');
						const lsRefUrl   = safeStorage.get('lead_ref_url');
						const lsFirstUrl = safeStorage.get('lead_first_url');

						if (lsSource) urlObj.searchParams.set('utm_source', lsSource);
						if (lsMedium) urlObj.searchParams.set('utm_medium', lsMedium);
						if (lsCampaign) urlObj.searchParams.set('utm_campaign', lsCampaign);
						if (lsContent) urlObj.searchParams.set('utm_content', lsContent);
						if (lsTerm) urlObj.searchParams.set('utm_term', lsTerm);
						if (lsRefId) urlObj.searchParams.set('ref_id', lsRefId);
						if (lsRefUrl) {
							const safeRef = sanitizeUrl(lsRefUrl);
							if (safeRef) urlObj.searchParams.set('ref_url', safeRef);
						}
						if (lsFirstUrl) {
							const safeFirst = sanitizeUrl(lsFirstUrl);
							if (safeFirst) urlObj.searchParams.set('first_url', safeFirst);
						}

						const htmlLang = document.documentElement.lang ? document.documentElement.lang.toLowerCase() : '';
						const locale = htmlLang.startsWith('en') ? 'EN' : 'ID';
						urlObj.searchParams.set('locale', locale);

						interactable.href = urlObj.toString();
					} catch (error) {}
				}
			}, { passive: false, capture: true });
		});

		populateFormInputs();
		setTimeout(populateFormInputs, 300);
		setTimeout(populateFormInputs, 1000);
		setTimeout(populateFormInputs, 2000);

		document.addEventListener('focusin', function (e) {
			if (e.target && e.target.closest && e.target.closest('form')) {
				populateFormInputs();
			}
		});

		document.addEventListener('submit', function (e) {
			populateFormInputs();
			const form = e.target;
			if (form && form.classList && form.classList.contains('wpcf7-form')) {
				if (form.dataset.submitting === 'true') {
					e.preventDefault();
					e.stopImmediatePropagation();
					return false;
				}
				form.dataset.submitting = 'true';
				const submitBtn = form.querySelector('[type="submit"], button[type="submit"]');
				if (submitBtn) {
					submitBtn.style.pointerEvents = 'none';
					submitBtn.style.opacity = '0.6';
				}
			}
		}, true);

		document.addEventListener('wpcf7beforesubmit', function (e) {
			populateFormInputs();
			const form = e.target;
			if (form) {
				form.dataset.submitting = 'true';
				const submitBtn = form.querySelector('[type="submit"], button[type="submit"]');
				if (submitBtn) {
					submitBtn.disabled = true;
					submitBtn.style.pointerEvents = 'none';
					submitBtn.style.opacity = '0.6';
				}
			}
		});

		['wpcf7submit', 'wpcf7mailsent', 'wpcf7mailfailed', 'wpcf7invalid'].forEach(function (evt) {
			document.addEventListener(evt, function (e) {
				const form = e.target;
				if (form) {
					delete form.dataset.submitting;
					const submitBtn = form.querySelector('[type="submit"], button[type="submit"]');
					if (submitBtn) {
						submitBtn.disabled = false;
						submitBtn.style.pointerEvents = '';
						submitBtn.style.opacity = '';
					}
				}
			});
		});

		if (window.jQuery) {
			jQuery(document).on('elementor/popup/show', function () {
				populateFormInputs();
			});
		}
	}

	function catchBitrixTrace() {
		let traceFound = false;

		if (typeof window.b24Tracker !== 'undefined' && window.b24Tracker.guest) {
			try {
				const liveTrace = window.b24Tracker.guest.getTrace();

				if (liveTrace && liveTrace.length > 10) {
					if (document.hasFocus()) {
						safeStorage.set('lead_b24_trace', liveTrace);
					}

					const traceInputs = document.querySelectorAll('input[name="b24_trace"]');
					traceInputs.forEach(input => { input.value = liveTrace; });
					traceFound = true;
				}
			} catch (err) {}
		}

		if (!traceFound) {
			let staleTrace = safeStorage.get('lead_b24_trace');
			if (staleTrace) {
				try {
					let traceObj = JSON.parse(staleTrace);
					// Prototype pollution guard
					if (traceObj && typeof traceObj === 'object' && !Array.isArray(traceObj) && (!traceObj.constructor || traceObj.constructor === Object)) {
						let currentUrl = window.location.origin + window.location.pathname;

						if (traceObj.url !== currentUrl) {
							traceObj.url = currentUrl;

							if (!traceObj.pages) traceObj.pages = { list: [] };
							if (!traceObj.pages.list) traceObj.pages.list = [];

							let lastPage = traceObj.pages.list[traceObj.pages.list.length - 1];
							if (!lastPage || lastPage[0] !== currentUrl) {
								let ts = Math.floor(Date.now() / 1000);
								let title = document.title ? document.title.substring(0, 40) : '';

								traceObj.pages.list.push([currentUrl, ts, title]);
							}

							staleTrace = JSON.stringify(traceObj);
						}

						const traceInputs = document.querySelectorAll('input[name="b24_trace"]');
						traceInputs.forEach(input => { input.value = staleTrace; });
					}
				} catch (e) {}
			}
		}

		setTimeout(catchBitrixTrace, 1500);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initAttribution();
			catchBitrixTrace();
		});
	} else {
		initAttribution();
		catchBitrixTrace();
	}
})();
