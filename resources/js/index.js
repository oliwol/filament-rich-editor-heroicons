import {Node} from '@tiptap/core';

const SIZE_MAP = { sm: 16, md: 24, lg: 32, xl: 48 };

function applyAlignment(el, align) {
    el.style.display = 'inline-block';
    el.style.verticalAlign = 'middle';
    el.style.float = '';
    el.style.marginLeft = '';
    el.style.marginRight = '';
    el.style.justifyContent = '';

    switch (align) {
        case 'left':
            el.style.float = 'left';
            el.style.marginRight = '0.5rem';
            break;
        case 'right':
            el.style.float = 'right';
            el.style.marginLeft = '0.5rem';
            break;
        case 'center':
            el.style.display = 'flex';
            el.style.justifyContent = 'center';
            break;
    }
}

function applySize(el, size) {
    const px = SIZE_MAP[size] || 24;
    const svg = el.querySelector('svg');
    if (svg) {
        svg.style.width = px + 'px';
        svg.style.height = px + 'px';
        svg.style.verticalAlign = 'middle';
    }
}

export default Node.create({
    name: 'heroicon',
    group: 'inline',
    inline: true,
    atom: true,

    addAttributes() {
        return {
            icon: {
                default: null,
                parseHTML: el => el.getAttribute('data-icon'),
                renderHTML: attrs => ({
                    'data-icon': attrs.icon,
                }),
            },
            svg: {
                default: null,
                parseHTML: el => el.getAttribute('data-svg'),
                renderHTML: attrs => ({
                    'data-svg': attrs.svg,
                }),
            },
            align: {
                default: 'inline',
                parseHTML: el => el.getAttribute('data-align') || 'inline',
                renderHTML: attrs => ({
                    'data-align': attrs.align || 'inline',
                }),
            },
            size: {
                default: 'md',
                parseHTML: el => el.getAttribute('data-size') || 'md',
                renderHTML: attrs => ({
                    'data-size': attrs.size || 'md',
                }),
            },
            style: {
                default: 'outline',
                parseHTML: el => el.getAttribute('data-style') || 'outline',
                renderHTML: attrs => ({
                    'data-style': attrs.style || 'outline',
                }),
            },
        }
    },

    parseHTML() {
        return [{ tag: 'span[data-svg]' }];
    },

    renderHTML({ HTMLAttributes }) {
        return ['span', HTMLAttributes, 0 ];
    },

    addNodeView() {
        return ({ node }) => {
            const span = document.createElement('span');
            span.innerHTML = node.attrs.svg || '<span>[Icon SVG missing!]</span>';

            applyAlignment(span, node.attrs.align || 'inline');
            applySize(span, node.attrs.size || 'md');

            return {
                dom: span,
            };
        };
    },

    addCommands() {
        return {
            heroicon:
                attrs =>
                    ({ commands }) =>
                        commands.insertContent({
                            type: 'heroicon',
                            attrs: attrs,
                        }),
        };
    },
});