export const CommaString = {
    toAttribute: (value) => value.toString().split(','),
    fromAttribute: (value) => value.join(','),
};

export default CommaString;