CKEDITOR.plugins.add('fixed', {
    init: function () {
        window.addEventListener('scroll', function () {

            if (CKEDITOR.instances[window.eventBus.$data.activeCkEditorId] != null) {
                let ckeditor = CKEDITOR.instances[window.eventBus.$data.activeCkEditorId]
                var toolbar = document.getElementById(ckeditor.id + '_top')
                var inner = toolbar.parentElement

                toolbar.style.width = "998px"
                toolbar.style.top = "0px"
                toolbar.style.left = "0px"
                toolbar.style.right = "0px"
                toolbar.style.margin = "0 auto"
                toolbar.style.boxSizing = "border-box"

                if (ckeditor.element.$.parentElement.getBoundingClientRect().top < 75) {
                    toolbar.style.position = "fixed"
                    toolbar.style.top = "0px"
                    inner.style.position = "relative"
                } else {
                    toolbar.style.position = "relative"
                }
            }
        }, false)
    }
})
