const { registerBlockType } = wp.blocks;
const { withAPIData } = wp.components;

registerBlockType( 'my-plugin/latest-post', {
    title: 'Latest Post',
    icon: 'megaphone',
    category: 'widgets',

    edit: withAPIData( () => {
        return {
            posts: '/wp/v2/posts?per_page=1'
        };
    } )( ( { posts, className } ) => {
        if ( ! posts.data ) {
            return "loading !";
        }
        if ( posts.data.length === 0 ) {
            return "No posts";
        }
        var post = posts.data[ 0 ];

        return <a className={ className } href={ post.link }>
            { post.title.rendered }
        </a>;
    } ),

    save() {
        // Rendering in PHP
        return null;
    },
} );