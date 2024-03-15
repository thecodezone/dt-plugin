// Import Navigo
import Navigo from 'navigo';
import $route from './stores/route';
import $reference, {$getReferenceRoute, $resetReference, $setReferenceRoute} from './stores/reference';


export default () => {
    const $router = new Navigo(`/`, {
        hash: true
    });

    $router
        .on({
            '/': function () {
                $resetReference()
                $route.set('tbp-reader')
            },
            '/:reference': function (info) {
                if (!info.hashstring) {
                    $resetReference()
                } else {
                    $setReferenceRoute(info.data.reference)
                }

                $route.set('tbp-reader')
            }
        })
        .resolve();

    if ($reference.get()) {
        $router.navigate($getReferenceRoute())
    }


    return $router;
}