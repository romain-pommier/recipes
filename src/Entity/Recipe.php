<?php

namespace App\Entity;

use function array_reduce;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function nl2br;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"title"},
 *     message = "Une recette possède déjà ce titre, merci de le modifier")
 */
class Recipe
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=255, minMessage="Le titre doit faire plus de 3 caractères !", maxMessage="Le titre ne peut pas faire plus de 255 caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=20, minMessage="La recette doit faire plus de 3 caractères !")
     */
    private $content;

    /**
     * @ORM\Column(type="integer")
     */
    private $people;

    /**
     * @ORM\Column(type="integer")
     */
    private $cookingTime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mealStyle;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string|null
     */
    private $coverImageName;

    /**
     * @var File|null
     * @Assert\Image(mimeTypes="image/jpeg", mimeTypesMessage="Le format de l'image n'est pas valide")
     * @vich\UploadableField(mapping="recipe_coverimage", fileNameProperty="coverImageName")
     */
    private $coverImageFile;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RecipePicture", mappedBy="recipe", orphanRemoval=true)
     * @Assert\Valid()
     */
    private $recipePictures;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Ingredient", mappedBy="recipe")
     *
     */
    private $ingredients;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="recipe", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->recipePictures = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


    /**
     * Permet d'initialiser le slug
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeSlug(){
        if(empty($this->slug)){

            $slug = new Slugify();
            $this->slug = $slug->slugify($this->title);
        }
    }

    /**
     * récupération d'un commentaire en relation avec un user
     *
     * @param User $author
     * @return |null
     */
    public function getCommentFromAuthor(User $author){
        foreach ($this->comments  as $comment){
            if($comment->getAuthor() == $author) {
                return $comment;

            }
        }
        return null;
    }

    public function getAvgRatings(){
        $sum = array_reduce($this->comments->toArray(), function($total, $comment){
            return $total + $comment->getRating();
        },0);

        if(count($this->comments) > 0){
            $moyenne = $sum / count($this->comments);
            return $moyenne;
        }
        else{
            return 0;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = nl2br($content);

        return $this;
    }

    public function getPeople(): ?int
    {
        return $this->people;
    }

    public function setPeople(int $people): self
    {
        $this->people = $people;

        return $this;
    }

    public function getCookingTime(): ?int
    {
        return $this->cookingTime;
    }

    public function setCookingTime(int $cookingTime): self
    {
        $this->cookingTime = $cookingTime;

        return $this;
    }

    public function getMealStyle(): ?string
    {
        return $this->mealStyle;
    }

    public function setMealStyle(string $mealStyle): self
    {
        $this->mealStyle = $mealStyle;

        return $this;
    }



    /**
     * @return Collection|RecipePicture[]
     */
    public function getRecipePictures(): Collection
    {
        return $this->recipePictures;
    }

    public function addRecipePicture(RecipePicture $recipePicture): self
    {
        if (!$this->recipePictures->contains($recipePicture)) {
            $this->recipePictures[] = $recipePicture;
            $recipePicture->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipePicture(RecipePicture $recipePicture): self
    {
        if ($this->recipePictures->contains($recipePicture)) {
            $this->recipePictures->removeElement($recipePicture);
            // set the owning side to null (unless already changed)
            if ($recipePicture->getRecipe() === $this) {
                $recipePicture->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ingredient[]
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->addRecipe($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        if ($this->ingredients->contains($ingredient)) {
            $this->ingredients->removeElement($ingredient);
            $ingredient->removeRecipe($this);
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setRecipe($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getRecipe() === $this) {
                $comment->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCoverImageName()
    {
        return $this->coverImageName;
    }

    /**
     * @param string|null $coverImageName
     */
    public function setCoverImageName($coverImageName)
    {
        $this->coverImageName = $coverImageName;
    }

    /**
     * @return File|null
     */
    public function getCoverImageFile()
    {
        return $this->coverImageFile;
    }

    /**
     * @param File|null $coverImageFile
     */
    public function setCoverImageFile($coverImageFile = null)
    {

        $this->coverImageFile = $coverImageFile;
        //débug vich_uploader changement de la date de modification pour la persistance
        if ($this->coverImageFile instanceof UploadedFile) {
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
