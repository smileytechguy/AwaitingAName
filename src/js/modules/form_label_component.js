// This is solely for templating.  Use by creating and adding .children[0] to your DOM.
class FormLabel extends HTMLElement {
	constructor(properties) {
		super();

		// decide if the HTML was created beforehand (e.g. from server) or without attributed (e.g. document.createElement)
		if (properties != undefined) {
			this.properties = properties;
		} else if (this.getAttribute("data-properties") != null) {
			this.properties = JSON.parse(this.getAttribute("data-properties"));
		} else {
			throw new Error("Element created without properties.");
		}

		window.log(this.constructor.name, "Constructing a label object to represent "+this.properties.distinguisher);

		this.appendChild((() => {
			var $$a = document.createElement('label');
			$$a.setAttribute('for', this.properties.formDistinguisher + '-input-' + this.properties.distinguisher);
			$$a.setAttribute('class', this.properties.hasOwnProperty('primary') && this.properties.primary || this.properties.hasOwnProperty('valueIsPrefilled') && this.properties.valueIsPrefilled ? ' active' : '');
			$$a.appendChildren(this.properties.label);
			var $$c = document.createElement('span');
			$$c.setAttribute('class', 'red-text' + (this.properties.required ? '' : ' hide'));
			$$a.appendChild($$c);
			var $$d = document.createTextNode('\xA0*');
			$$c.appendChild($$d);
			return $$a;
		})());
	}

	connectedCallback() {
		throw new Error("FormLabel should NEVER be added to the DOM.  It is for templating only.  If you wish to use it, create an instance and add .children[0] (the standard label element itself) to your DOM.");
	}
}
