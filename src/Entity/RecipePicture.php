<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipePictureRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class RecipePicture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string|null
     */
    private $recipePictureName;
    /**
     * @var File|null
     * @Assert\Image(mimeTypes="image/jpeg", mimeTypesMessage="Le format de l'image n'est pas valide")
     * @vich\UploadableField(mapping="recipe_picture", fileNameProperty="recipePictureName")
     */
    private $recipePictureFile;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, minMessage = "Le titre de l'image doit faire minimun 3 caractères")
     *
     */
    private $caption;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Recipe", inversedBy="recipePictures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecipePictureName()
    {
        return $this->recipePictureName;
    }

    /**
     * @param string|null $recipePictureName
     */
    public function setRecipePictureName($recipePictureName)
    {
        $this->recipePictureName = $recipePictureName;
    }

    /**
     * @return File|null
     */
    public function getRecipePictureFile()
    {
        return $this->recipePictureFile;
    }

    /**
     * @param File|null $recipePictureFile
     */
    public function setRecipePictureFile($recipePictureFile = null)
    {
        $this->recipePictureFile = $recipePictureFile;
        //débug vich_uploader changement de la date de modification pour la persistance
        if ($this->recipePictureFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


}
