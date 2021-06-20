let createIntervalWrapper = function () {
    let interval = createIntervalInput();
    let remButton = createRemovalButton();
    let wrapper = document.createElement('span');
    wrapper.classList.add('donor-interval-wrapper');
    wrapper.appendChild(interval);
    wrapper.appendChild(remButton);
    return wrapper;
};

let createIntervalInput = function () {
    let interval = document.createElement('input');
    interval.type = 'number';
    return interval;
};

let createRemovalButton = function () {
    let remButton = document.createElement('button');
    remButton.type = 'button';
    remButton.textContent = 'X';
    remButton.classList.add('donor-interval-remove-button');
    remButton.onclick = removeInterval;
    return remButton;
};

let removeInterval = function () {
    let parent = this.parentElement;
    parent.remove();
};

let addNewDonorInterval = function () {
    let wrapper = createIntervalWrapper();
    document.getElementById('donor-interval-container').appendChild(wrapper);
};
