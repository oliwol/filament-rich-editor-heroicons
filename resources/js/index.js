import {Node} from '@tiptap/core';

const SIZE_MAP = { sm: 16, md: 24, lg: 32, xl: 48 };

function stripFilamentClasses(el) {
    const svg = el.querySelector('svg');
    if (svg) {
        svg.removeAttribute('class');
    }
}

function injectProseMirrorFix() {
    if (document.getElementById('heroicon-pm-fix')) return;
    const style = document.createElement('style');
    style.id = 'heroicon-pm-fix';
    style.textContent = '.ProseMirror img.ProseMirror-separator { display: inline !important; vertical-align: middle; width: 0; height: 0; } .fi-prose .ProseMirror span[contenteditable="false"] { margin-top: 0 !important; }';
    document.head.appendChild(style);
}

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

function applyColor(el, color) {
    el.style.color = (!color || color === 'currentColor') ? '' : color;
}

function applySize(el, size) {
    const px = SIZE_MAP[size] || 24;
    const svg = el.querySelector('svg');
    if (svg) {
        svg.style.width = px + 'px';
        svg.style.height = px + 'px';
        svg.style.display = 'block';
    }
}

function findToolbarButton(editor, actionName) {
    const editorEl = editor.view.dom;
    const wrapper = editorEl.closest('[x-data]');
    if (!wrapper) return null;

    const buttons = wrapper.querySelectorAll('button.fi-fo-rich-editor-tool');
    for (const btn of buttons) {
        const clickAttr = btn.getAttribute('x-on:click') || btn.getAttribute('@click');
        if (clickAttr && clickAttr.includes(actionName)) {
            return btn;
        }
    }
    return null;
}

export default Node.create({
    name: 'heroicon',
    group: 'inline',
    inline: true,
    atom: true,
    selectable: true,

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
            color: {
                default: '#000000',
                parseHTML: el => el.getAttribute('data-color') || '#000000',
                renderHTML: attrs => ({
                    'data-color': attrs.color || '#000000',
                }),
            },
            ariaLabel: {
                default: null,
                parseHTML: el => el.getAttribute('data-aria-label'),
                renderHTML: attrs => attrs.ariaLabel
                    ? { 'data-aria-label': attrs.ariaLabel }
                    : {},
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
        return ({ editor, node, getPos }) => {
            const span = document.createElement('span');
            span.innerHTML = node.attrs.svg || '<span>[Icon SVG missing!]</span>';

            injectProseMirrorFix();
            stripFilamentClasses(span);
            applyAlignment(span, node.attrs.align || 'inline');
            applySize(span, node.attrs.size || 'md');
            applyColor(span, node.attrs.color || '#000000');

            if (editor.isEditable) {
                span.style.cursor = 'pointer';

                span.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const pos = getPos();
                    if (typeof pos !== 'number') return;

                    editor.commands.setNodeSelection(pos);

                    setTimeout(() => {
                        const btn = findToolbarButton(editor, 'addHeroicon');
                        if (btn) btn.click();
                    }, 0);
                });
            }

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