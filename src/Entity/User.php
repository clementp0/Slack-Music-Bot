<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Playlist::class, mappedBy="creator", orphanRemoval=true)
     */
    private $playlists;

    /**
     * @ORM\ManyToMany(targetEntity=Playlist::class, inversedBy="users")
     */
    private $followPlaylist;

    public function __construct()
    {
        $this->playlists = new ArrayCollection();
        $this->followPlaylist = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Playlist[]
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): self
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists[] = $playlist;
            $playlist->setCreator($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        if ($this->playlists->removeElement($playlist)) {
            // set the owning side to null (unless already changed)
            if ($playlist->getCreator() === $this) {
                $playlist->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Playlist[]
     */
    public function getFollowPlaylist(): Collection
    {
        return $this->followPlaylist;
    }

    public function addFollowPlaylist(Playlist $followPlaylist): self
    {
        if (!$this->followPlaylist->contains($followPlaylist)) {
            $this->followPlaylist[] = $followPlaylist;
        }

        return $this;
    }

    public function removeFollowPlaylist(Playlist $followPlaylist): self
    {
        $this->followPlaylist->removeElement($followPlaylist);

        return $this;
    }
}
