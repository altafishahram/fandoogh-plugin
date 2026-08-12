(function () {
    'use strict';

    const config = window.faFandooghCalculator || {};
    const formatter = new Intl.NumberFormat('fa-IR', {maximumFractionDigits: 0});

    function money(value) {
        return formatter.format(Math.round(Number(value) || 0)) + ' تومان';
    }

    function post(data) {
        return fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            body: new URLSearchParams(data)
        }).then(function (response) {
            return response.json().then(function (result) {
                if (!response.ok || !result || !result.success) {
                    throw new Error(result && result.data && result.data.message
                        ? result.data.message
                        : 'در ارتباط با سرور خطایی رخ داد.');
                }
                return result.data;
            });
        });
    }

    function createOption(value, label) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        return option;
    }

    class FandooghCalculator {
        constructor(root) {
            this.root = root;
            this.product = root.querySelector('.fa-fandoogh-product');
            this.meters = root.querySelector('.fa-fandoogh-meters');
            this.variationsNode = root.querySelector('.fa-fandoogh-variations');
            this.feesNode = root.querySelector('.fa-fandoogh-fees');
            this.optionalFeesNode = root.querySelector('.fa-fandoogh-optional-fees');
            this.perMeterNode = root.querySelector('.fa-fandoogh-per-meter');
            this.totalNode = root.querySelector('.fa-fandoogh-total');
            this.orderButton = root.querySelector('.fa-fandoogh-order');
            this.statusNode = root.querySelector('.fa-fandoogh-status');
            this.data = null;
            this.variation = null;
            this.selectedOptionalFees = new Set();
            this.ctaAction = config.ctaAction || 'woocommerce_cart';
            this.ctaTarget = config.ctaTarget || '';
            this.unitLabel = config.unitLabel || 'متر';

            if (!this.product) return;
            this.product.addEventListener('change', () => this.loadProduct());
            this.meters.addEventListener('input', () => this.calculate());
            this.orderButton.addEventListener('click', () => this.submitAction());
            
            // Delegate change event for optional checkboxes
            if (this.optionalFeesNode) {
                this.optionalFeesNode.addEventListener('change', (e) => {
                    if (e.target.matches('input[type="checkbox"]')) {
                        if (e.target.checked) {
                            this.selectedOptionalFees.add(e.target.value);
                        } else {
                            this.selectedOptionalFees.delete(e.target.value);
                        }
                        this.calculate();
                    }
                });
            }
        }

        setStatus(message, error) {
            this.statusNode.textContent = message || '';
            this.statusNode.classList.toggle('is-error', Boolean(error));
        }

        setBusy(busy) {
            this.root.classList.toggle('is-loading', busy);
            this.product.disabled = busy;
            if (busy) this.orderButton.disabled = true;
        }

        reset() {
            this.data = null;
            this.variation = null;
            this.selectedOptionalFees.clear();
            this.variationsNode.replaceChildren();
            this.feesNode.replaceChildren();
            if (this.optionalFeesNode) this.optionalFeesNode.replaceChildren();
            this.perMeterNode.textContent = '—';
            this.totalNode.textContent = '—';
            this.orderButton.disabled = true;
            this.setStatus('', false);
        }

        loadProduct() {
            const productId = Number(this.product.value);
            this.reset();
            if (!productId) return;

            this.setBusy(true);
            this.setStatus('در حال دریافت مشخصات محصول…', false);
            post({
                action: config.productAction,
                nonce: config.nonce,
                product_id: productId
            }).then((data) => {
                this.data = data;
                this.renderAttributes();
                this.renderFees();
                this.updateVariation();
                this.setStatus('', false);
            }).catch((error) => {
                this.setStatus(error.message, true);
            }).finally(() => {
                this.setBusy(false);
                this.calculate();
            });
        }

        renderAttributes() {
            const fragment = document.createDocumentFragment();
            (this.data.attributes || []).forEach((attribute) => {
                const label = document.createElement('label');
                label.className = 'fa-fandoogh-field';
                const title = document.createElement('span');
                title.textContent = attribute.label;
                const select = document.createElement('select');
                select.className = 'fa-fandoogh-attribute';
                select.dataset.attribute = attribute.key;
                select.append(createOption('', 'انتخاب ' + attribute.label + '…'));
                (attribute.options || []).forEach((option) => {
                    select.append(createOption(option.value, option.label));
                });
                select.addEventListener('change', () => this.updateVariation());
                label.append(title, select);
                fragment.append(label);
            });
            this.variationsNode.replaceChildren(fragment);
        }

        renderFees() {
            const fees = this.data.fixed_prices || [];
            if (!fees.length) {
                this.feesNode.replaceChildren();
                if (this.optionalFeesNode) this.optionalFeesNode.replaceChildren();
                return;
            }

            // Mandatory fees
            const mandatoryFees = fees.filter(f => f.mode !== 'optional');
            if (mandatoryFees.length > 0) {
                const title = document.createElement('strong');
                title.textContent = config.labelMandatoryFees || 'هزینه‌های لحاظ‌شده';
                const list = document.createElement('div');
                list.className = 'fa-fandoogh-fees__list';
                mandatoryFees.forEach((fee) => {
                    const badge = document.createElement('span');
                    const suffix = fee.type === 'per_meter' ? ` / ${this.unitLabel}` : '';
                    badge.textContent = fee.title + ': ' + money(fee.price) + suffix;
                    list.append(badge);
                });
                this.feesNode.replaceChildren(title, list);
            } else {
                this.feesNode.replaceChildren();
            }

            // Optional fees
            if (this.optionalFeesNode) {
                const optionalFees = fees.filter(f => f.mode === 'optional');
                if (optionalFees.length > 0) {
                    const fragment = document.createDocumentFragment();
                    optionalFees.forEach((fee) => {
                        const label = document.createElement('label');
                        label.className = 'fa-fandoogh-optional-fee';
                        
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.value = fee.id;
                        
                        const titleWrap = document.createElement('span');
                        titleWrap.textContent = fee.title;
                        
                        const priceWrap = document.createElement('strong');
                        const suffix = fee.type === 'per_meter' ? ` / ${this.unitLabel}` : '';
                        priceWrap.textContent = '+' + money(fee.price) + suffix;
                        
                        label.append(checkbox, titleWrap, priceWrap);
                        fragment.append(label);
                    });
                    this.optionalFeesNode.replaceChildren(fragment);
                } else {
                    this.optionalFeesNode.replaceChildren();
                }
            }
        }

        updateVariation() {
            if (!this.data) return;
            const selects = Array.from(this.variationsNode.querySelectorAll('.fa-fandoogh-attribute'));
            const selected = {};
            let complete = true;
            selects.forEach((select) => {
                selected[select.dataset.attribute] = select.value;
                if (!select.value) complete = false;
            });

            this.variation = null;
            if (selects.length === 0 || complete) {
                this.variation = (this.data.variations || []).find((variation) => {
                    return Object.keys(selected).every((key) => {
                        const expected = variation.attributes[key] || '';
                        return expected === '' || expected === selected[key];
                    });
                }) || null;
            }

            if (complete && !this.variation) {
                this.setStatus('این ترکیب در حال حاضر موجود نیست.', true);
            } else {
                this.setStatus('', false);
            }
            this.calculate();
        }

        calculate() {
            const meters = Math.floor(Number(this.meters.value));
            if (!this.data || !this.variation || !(meters > 0)) {
                this.perMeterNode.textContent = '—';
                this.totalNode.textContent = '—';
                this.orderButton.disabled = true;
                return;
            }

            let perMeterFees = 0;
            let lumpSumFees = 0;
            
            (this.data.fixed_prices || []).forEach((fee) => {
                if (fee.mode === 'optional' && !this.selectedOptionalFees.has(fee.id)) {
                    return; // skip unselected optional fees
                }
                
                if (fee.type === 'per_meter') perMeterFees += Number(fee.price) || 0;
                else lumpSumFees += Number(fee.price) || 0;
            });

            const pricePerMeter = (Number(this.variation.price) || 0) + perMeterFees;
            const total = (pricePerMeter * meters) + lumpSumFees;
            this.perMeterNode.textContent = money(pricePerMeter);
            this.totalNode.textContent = money(total);
            this.orderButton.disabled = false;
        }

        submitAction() {
            const meters = Math.floor(Number(this.meters.value));
            if (!this.data || !this.variation || !(meters > 0)) return;

            if (this.ctaAction === 'woocommerce_cart') {
                this.addToCart(meters);
            } else if (this.ctaAction === 'contact_direct') {
                this.contactDirect(meters);
            } else if (this.ctaAction === 'scroll_to_form') {
                this.scrollToForm();
            }
        }
        
        contactDirect(meters) {
            const total = this.totalNode.textContent;
            const productName = this.product.options[this.product.selectedIndex].text;
            let message = `سلام، من درخواست پیش‌فاکتور برای محصول ${productName} دارم.\n`;
            message += `مقدار: ${meters} ${this.unitLabel}\n`;
            message += `مبلغ کل: ${total}`;
            const encodedMessage = encodeURIComponent(message);
            
            let url = '';
            let target = this.ctaTarget.trim();

            if (target.includes('{message}')) {
                url = target.replace('{message}', encodedMessage);
            } else if (target.startsWith('http')) {
                // If it's a raw web URL, open it as is
                url = target;
            } else if (target.startsWith('0') || target.startsWith('+')) {
                // Fallback: format phone number for WhatsApp
                if (target.startsWith('09')) {
                    target = '+98' + target.substring(1);
                }
                url = `https://wa.me/${target.replace(/[^\d+]/g, '')}?text=${encodedMessage}`;
            } else {
                url = `tel:${target.replace(/[^\d+]/g, '')}`;
            }
            
            window.open(url, '_blank');
        }
        
        scrollToForm() {
            const target = this.ctaTarget.trim();
            if (!target) return;
            const element = document.querySelector(target.startsWith('#') || target.startsWith('.') ? target : '#' + target);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                this.setStatus('فرم تماس یافت نشد.', true);
            }
        }

        addToCart(meters) {
            this.orderButton.disabled = true;
            this.setStatus('در حال ثبت پیش‌فاکتور…', false);
            
            let optionalFeesArray = Array.from(this.selectedOptionalFees);
            
            let formData = new URLSearchParams();
            formData.append('action', config.cartAction);
            formData.append('nonce', config.nonce);
            formData.append('product_id', this.data.product.id);
            formData.append('variation_id', this.variation.id);
            formData.append('meters', meters);
            optionalFeesArray.forEach(fee => formData.append('optional_fees[]', fee));

            fetch(config.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            }).then(function (response) {
                return response.json().then(function (result) {
                    if (!response.ok || !result || !result.success) {
                        throw new Error(result && result.data && result.data.message
                            ? result.data.message
                            : 'در ارتباط با سرور خطایی رخ داد.');
                    }
                    return result.data;
                });
            }).then((data) => {
                this.setStatus(data.message, false);
                window.location.href = data.cart_url;
            }).catch((error) => {
                this.setStatus(error.message, true);
                this.orderButton.disabled = false;
            });
        }
    }

    document.querySelectorAll('.fa-fandoogh-calculator').forEach(function (root) {
        new FandooghCalculator(root);
    });
})();
