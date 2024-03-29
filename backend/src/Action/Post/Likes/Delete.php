<?php

namespace App\Action\Post\Likes;

use App\Entity\Developer;
use App\Entity\Post;
use App\Service\LikeService;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class Delete
{
    /** @var LikeService $likeService */
    protected $likeService;
    /** @var View $view */
    protected $view;
    /** @var Security $security */
    protected $security;

    /**
     * Index constructor.
     * @param LikeService $likeService
     * @param View $view
     * @param Context $context
     * @param Security $security
     */
    public function __construct(
        LikeService $likeService,
        View $view,
        Context $context,
        Security $security
    ) {
        $this->likeService = $likeService;
        $this->view = $view;
        $this->security = $security;

        $this->view->setContext(
            $context->addGroups(['default', 'like'])
        );
    }

    /**
     * @SWG\Delete(
     *     summary="Delete a like",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @Model(type=App\Entity\Like::class, groups={"like"})
     *    ),
     *    @SWG\Response(
     *        response=403,
     *        description="Forbidden",
     *    ),
     * )
     * @SWG\Tag(name="Post")
     *
     * @param Post $post
     * @param Developer $developer
     * @return View
     */
    public function __invoke(Post $post, Developer $developer): View
    {
        $like = $this->likeService->findOneBy(['post' => $post, 'developer' => $developer]);

        if (!$like) {
            return $this->view->setStatusCode(Response::HTTP_OK);
        }

        if (!$this->security->isGranted('delete', $like)) {
            return $this->view->setStatusCode(Response::HTTP_FORBIDDEN);
        };

        $this->likeService->delete($like);

        return $this->view->setData($like);
    }
}
