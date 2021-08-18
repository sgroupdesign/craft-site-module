// For every Neo block, add a number to the title for much needed clarity
$(function() {
    if (typeof Neo !== 'undefined') {
        Garnish.on(Neo.Input, 'addBlock', {}, function(e) {
            var block = e.block;

            var $label = block.$topbarContainer.find('.blocktype');
            var blockCount = block.$container.siblings('.ni_block').length + 1;

            if ($label) {
                $label.html('<span class="neo-counter">#' + blockCount + '</span> ' + $label.text())
            }
        }.bind(this));

        Garnish.on(Neo.Input, 'removeBlock', {}, function(e) {
            var block = e.block;

            var $container = block.$container.parent();

            // Give it a sec for the block to be deleted
            setTimeout(function() {
                $container.find('.ni_block').each(function(index, block) {
                    var $block = $(block);
                    var $label = $block.find('.ni_block_topbar .neo-counter');

                    if ($label) {
                        $label.html('#' + (index + 1))
                    }
                });
            }, 600);
        }.bind(this));
    }
});
