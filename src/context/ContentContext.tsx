
import React, { createContext, useContext, useState, useEffect } from "react";
import { ContentItem } from "@/types/content";

interface ContentContextType {
  contents: ContentItem[];
  addContent: (content: Omit<ContentItem, "id" | "timestamp">) => void;
  deleteContent: (id: string) => void;
}

const ContentContext = createContext<ContentContextType | undefined>(undefined);

export const ContentProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [contents, setContents] = useState<ContentItem[]>(() => {
    const savedContents = localStorage.getItem("carousel-contents");
    return savedContents ? JSON.parse(savedContents) : [];
  });

  useEffect(() => {
    localStorage.setItem("carousel-contents", JSON.stringify(contents));
  }, [contents]);

  const addContent = (content: Omit<ContentItem, "id" | "timestamp">) => {
    const newContent: ContentItem = {
      ...content,
      id: Date.now().toString(),
      timestamp: Date.now(),
    };
    setContents((prev) => [newContent, ...prev]);
  };

  const deleteContent = (id: string) => {
    setContents((prev) => prev.filter((content) => content.id !== id));
  };

  return (
    <ContentContext.Provider value={{ contents, addContent, deleteContent }}>
      {children}
    </ContentContext.Provider>
  );
};

export const useContent = () => {
  const context = useContext(ContentContext);
  if (context === undefined) {
    throw new Error("useContent must be used within a ContentProvider");
  }
  return context;
};
