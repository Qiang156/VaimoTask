/**
 *
 * @type {{setJsInitBlockOptions: jsInitModifier.setJsInitBlockOptions, getInitBlockByKeyword: (function(*, *): null), setInitBlockOptionsInScriptTag: jsInitModifier.setInitBlockOptionsInScriptTag, getInitBlockOptionsObject: (function(*): any), BLOCK_TYPE_SCRIPT_TAG: number, getInitBlockType: ((function(*): Number)|*), BLOCK_TYPE_ATTR: number, setInitBlockOptionsAsAttr: jsInitModifier.setInitBlockOptionsAsAttr, mergeObjectsRecursive: (function(*, *): *)}}
 */
define([
        'jquery',
    ], function ($) {
    //'use strict';

    jsInitModifier = {

        BLOCK_TYPE_ATTR: 1,
        BLOCK_TYPE_SCRIPT_TAG: 2,

        /**
         * Changes JS component init options before component initialized
         *
         * @param target String
         * @param options Object
         *
         * @return void
         */
        setJsInitBlockOptions: function (target, options) {

            var initBlocks, initBlock;
            var initBlockType = this.getInitBlockType(target);

            if (initBlockType === this.BLOCK_TYPE_ATTR) {
                initBlock = document.querySelector(target);
            } else if (initBlockType === this.BLOCK_TYPE_SCRIPT_TAG) {
                //initBlocks = document.querySelectorAll('[type="text/x-magento-init"]');
                initBlocks = document.querySelectorAll('script');
                initBlock = this.getInitBlockByKeyword(initBlocks, target);
            }

            if (initBlock === null) {
                console.error('jsInitModifier: cannot find init block "' + target + '"');
                return;
            }

            if (initBlockType === this.BLOCK_TYPE_ATTR) {
                this.setInitBlockOptionsAsAttr(initBlock, options);
            } else if (initBlockType === this.BLOCK_TYPE_SCRIPT_TAG) {
                this.setInitBlockOptionsInScriptTag(initBlock, options);
            }
        },

        /**
         * @param target String
         *
         * @return {Number}
         */
        getInitBlockType: function (target) {
            try {
                var s = document.querySelector(target);

                if (s !== null) {
                    return this.BLOCK_TYPE_ATTR;
                } else {
                    return this.BLOCK_TYPE_SCRIPT_TAG;
                }
            } catch (e) {
                return this.BLOCK_TYPE_SCRIPT_TAG;
            }
        },

        /**
         * @param initBlocks Array
         * @param keyword string
         *
         * @return {Element|null}
         */
        getInitBlockByKeyword: function (initBlocks, keyword) {
            var block = null;

            initBlocks.forEach(function (initBlock) {
                console.log(initBlock.outerHTML);
                if (initBlock.outerHTML.indexOf(keyword) !== -1) {
                    block = initBlock;
                }
            });

            return block;
        },

        /**
         * @param jsonOptionsAsString String
         *
         * @return {Object}
         */
        getInitBlockOptionsObject: function (jsonOptionsAsString) {
            return JSON.parse(jsonOptionsAsString);
        },

        /**
         * @param initBlock Element
         * @param initBlockOptionsCustom Object
         *
         * @return {void}
         */
        setInitBlockOptionsAsAttr: function (initBlock, initBlockOptionsCustom) {
            var initBlockOptionsInitial = this.getInitBlockOptionsObject(initBlock.dataset.mageInit);
            var customInitOptions = this.mergeObjectsRecursive(initBlockOptionsInitial, initBlockOptionsCustom);

            initBlock.dataset.mageInit = JSON.stringify(customInitOptions);
        },

        /**
         * @param initBlock Element
         * @param initBlockOptionsCustom Object
         *
         * @return {void}
         */
        setInitBlockOptionsInScriptTag: function (initBlock, initBlockOptionsCustom) {
            var initBlockOptionsInitial = this.getInitBlockOptionsObject(initBlock.innerHTML);
            var customInitOptions = this.mergeObjectsRecursive(initBlockOptionsInitial, initBlockOptionsCustom);

            initBlock.innerHTML = JSON.stringify(customInitOptions);
        },

        /**
         * Recursively merge properties of two objects
         *
         * @param obj1 Object
         * @param obj2 Object
         *
         * @return {Object}
         */
        mergeObjectsRecursive: function (obj1, obj2) {
            var self = this;

            for (var prop in obj2) {
                try {
                    // Property in destination object set; update its value.
                    if (obj2[prop].constructor === Object) {
                        obj1[prop] = self.mergeObjectsRecursive(obj1[prop], obj2[prop]);

                    } else {
                        obj1[prop] = obj2[prop];
                    }
                } catch (e) {
                    // Property in destination object not set; create it and set its value.
                    obj1[prop] = obj2[prop];
                }
            }

            return obj1;
        }
    }

});

