$(document).ready(function () {

    var actionsExtract = {
        init: function () {
            var self = this;
            OCA.Files.fileActions.registerAction({
                name: 'extractzip',
                displayName: t('extract', 'Extract here'),
                mime: 'application/zip',
                permissions: OC.PERMISSION_UPDATE,
                type: OCA.Files.FileActions.TYPE_DROPDOWN,
                iconClass: 'icon-extract',
                actionHandler: function (filename, context) {
                    var data = {
                        nameOfFile: filename,
                        directory: context.dir,
                        external: context.fileInfoModel.attributes.mountType && context.fileInfoModel.attributes.mountType.startsWith("external") ? 1 : 0,
                        type: 'zip'
                    };
                    var tr = context.fileList.findFileEl(filename);
                    context.fileList.showFileBusyState(tr, true);
                    $.ajax({
                        type: "POST",
                        async: "false",
                        url: OC.filePath('custom-extract', 'ajax', 'extract.php'),
                        data: data,
                        success: function (response) {
                            if (response.code == 1) {
                                context.fileList.reload();
                            } else {
                                context.fileList.showFileBusyState(tr, false);
                                OC.dialogs.alert(
                                    t('custom-extract', response.desc),
                                    t('custom-extract', 'Error extracting ' + filename)
                                );
                            }
                        }
                    });
                }
            });

            // RAR
            OCA.Files.fileActions.registerAction({
                name: 'extractrar',
                displayName: t('extract', 'Extract here'),
                mime: 'application/x-rar-compressed',
                permissions: OC.PERMISSION_UPDATE,
                type: OCA.Files.FileActions.TYPE_DROPDOWN,
                iconClass: 'icon-extract',
                actionHandler: function (filename, context) {
                    var data = {
                        nameOfFile: filename,
                        directory: context.dir,
                        external: context.fileInfoModel.attributes.mountType && context.fileInfoModel.attributes.mountType.startsWith("external") ? 1 : 0,
                        type: 'rar'
                    };
                    var tr = context.fileList.findFileEl(filename);
                    context.fileList.showFileBusyState(tr, true);
                    $.ajax({
                        type: "POST",
                        async: "false",
                        url: OC.filePath('extract', 'ajax', 'extract.php'),
                        data: data,
                        success: function (element) {
                            element = element.replace(/null/g, '');
                            console.log(element);
                            response = JSON.parse(element);
                            if (response.code == 1) {
                                context.fileList.reload();
                            } else {
                                context.fileList.showFileBusyState(tr, false);
                                OC.dialogs.alert(
                                    t('extract', response.desc),
                                    t('extract', 'Error extracting ' + filename)
                                );
                            }
                        }
                    });
                }
            });
            // TAR
            //'application/x-tar', 'application/x-7z-compressed'
            //var types = [];
            var types = ['application/x-tar', 'application/x-7z-compressed', 'application/x-bzip2', 'application/x-deb', 'application/x-gzip'];
            types.forEach(type => {
                OCA.Files.fileActions.registerAction({
                    name: 'extractOthers',
                    displayName: t('extract', 'Extract here'),
                    mime: type,
                    permissions: OC.PERMISSION_UPDATE,
                    type: OCA.Files.FileActions.TYPE_DROPDOWN,
                    iconClass: 'icon-extract',
                    actionHandler: function (filename, context) {
                        var data = {
                            nameOfFile: filename,
                            directory: context.dir,
                            external: context.fileInfoModel.attributes.mountType && context.fileInfoModel.attributes.mountType.startsWith("external") ? 1 : 0,
                            type: 'other'
                        };
                        var tr = context.fileList.findFileEl(filename);
                        context.fileList.showFileBusyState(tr, true);
                        $.ajax({
                            type: "POST",
                            async: "false",
                            url: OC.filePath('extract', 'ajax', 'extract.php'),
                            data: data,
                            success: function (element) {
                                element = element.replace('null', '');
                                response = JSON.parse(element);
                                if (response.code == 1) {
                                    context.fileList.reload();
                                } else {
                                    context.fileList.showFileBusyState(tr, false);
                                    OC.dialogs.alert(
                                        t('extract', response.desc),
                                        t('extract', 'Error extracting ' + filename)
                                    );
                                }
                            }
                        });
                    }
                });
            });
        },
    }
    actionsExtract.init();
});

