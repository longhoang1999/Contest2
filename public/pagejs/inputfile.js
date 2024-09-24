class FileChooser {
    constructor(element, settings) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }

        this.settings = FileChooser.getSettings(settings);
        this.originalInput = element;
        this.wrapper = FileChooser.createWrapper();
        this.input = FileChooser.createInput(this.settings.placeholder);
        this.clearButton = FileChooser.createClearButton();

        this.appendElements();
        this.attachListeners();
    }
    setText(text) {
        this.input.value = text;
    }
    reset() {
        this.wrapper.reset();
    }
    open() {
        this.originalInput.click();
    }
    attachListeners() {
        this.wrapper.addEventListener('click', (ev) => {
            ev.preventDefault();
            this.open();
        });
        this.wrapper.addEventListener('submit', (ev) => ev.preventDefault());

        this.clearButton.addEventListener('click', (ev) => {
            ev.stopPropagation();
            this.reset();
        });

        this.originalInput.addEventListener('click', (ev) => ev.stopPropagation());

        this.originalInput.addEventListener('change', (ev) => {
            this.setText(ev.target.value);
        });
    }
    appendElements() {
        let parent = this.originalInput.parentNode;

        this.originalInput.classList.add('file-chooser-hidden');
        this.wrapper.appendChild(this.input);
        this.wrapper.appendChild(this.clearButton);
        parent.insertBefore(this.wrapper, this.originalInput);
        this.wrapper.appendChild(this.originalInput);
    }
    static getDefaults() {
        return {
            buttonText: 'Browse',
            placeholder: 'Please choose a file'
        }
    }
    static getSettings(settings) {
        return {
            ...FileChooser.getDefaults(),
            ...settings
        };
    }
    static createWrapper() {
        let wrapper = document.createElement('form');
        wrapper.classList.add('file-chooser');
        return wrapper;
    }
    static createInput(placeholder) {
        let input = document.createElement('input');
        input.setAttribute('readonly', true);
        input.setAttribute('placeholder', placeholder);
        input.classList.add('file-chooser-input');
        return input;
    }
    static createClearButton() {
        let clearButton = document.createElement('button');
        clearButton.classList.add('file-chooser-clear');
        return clearButton;
    }
}

let fileBorwser = new FileChooser('.file-browser', {});