export default (callback) => {
    const options = {
        attributes: true
    }

    const observer = new MutationObserver((mutationList, observer) => {
        mutationList.forEach(function (mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                callback(mutation.target.classList.contains('dark'));
            }
        })
    });
    observer.observe(document.documentElement, options);
    callback(document.documentElement.classList.contains('dark'));
};
